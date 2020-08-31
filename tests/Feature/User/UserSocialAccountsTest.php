<?php

namespace Tests\Feature\User;

use App\Http\Controllers\UserSocialAccountController;
use App\Notifications\NewReferredUserNotification;
use App\Notifications\UserHasRegisteredNotification;
use App\User;
use App\UserEmail;
use App\UserSocialAccount;
use Carbon\Carbon;
use ErrorException;
use Exception;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Laravel\Socialite\Facades\Socialite;
use Mockery;
use RuntimeException;
use Tests\TestCase;

class UserSocialAccountsTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testIfNotRegistered()
	{
		Notification::fake();

		$email = $this->faker->email;
		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;

		UserEmail::email($email)
			->delete();

		$this->assertNull(UserEmail::email($email)->first());

		$this->mockSocialiteFacade($email, $token, 'facebook', $provider_user_id, 'http://dev.litlife.club/img/nocover4.jpeg');

		$this->get('/auth/facebook/callback')
			->assertRedirect(route('welcome'));

		$email = UserEmail::email($email)->first();
		$user = $email->user;
		$social_account = $user->social_accounts()->first();

		$this->assertNotNull($email);
		$this->assertNotNull($user);
		$this->assertNotNull($social_account);
		$this->assertEquals($provider_user_id, $social_account->provider_user_id);
		$this->assertEquals($token, $social_account->access_token);
		$this->assertNotNull($user->avatar()->first());

		$this->assertAuthenticatedAs($user);

		Notification::assertSentTo(
			$user,
			UserHasRegisteredNotification::class,
			function ($notification, $channels) use ($user, $email) {

				$this->assertContains('mail', $channels);

				$mail = $notification->toMail($user);

				$this->assertEquals(__('notification.user_has_registered.subject'), $mail->subject);

				$this->assertEquals(__('notification.user_has_registered.line'), $mail->introLines[0]);
				$this->assertEquals(__('notification.user_has_registered.line2'), $mail->introLines[1]);

				$this->assertEquals(__('notification.user_has_registered.line3', [
					'email' => $email->email
				]), $mail->introLines[2]);

				$this->assertEquals(__('notification.user_has_registered.line4', [
					'password' => $notification->password
				]), $mail->introLines[3]);

				$this->assertEquals(4, count($mail->introLines));

				$this->assertEquals(route('profile', ['user' => $user]), $mail->actionUrl);
				$this->assertEquals(__('notification.user_has_registered.action'), $mail->actionText);

				$result = Auth::guard()->attempt([
					'login' => $email->email,
					'password' => $notification->password
				]);

				$this->assertTrue($result);

				return $notification->user->id == $user->id;
			}
		);
	}

	/**
	 * Mock the Socialite Factory, so we can hijack the OAuth Request.
	 * @param string $email
	 * @param string $token
	 * @param int $id
	 */
	public function mockSocialiteFacade($email = null, $token = 'foo', $provider_name = 'google', $provider_user_id = 1, $avatar_url = '/img/nocover4.jpeg')
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('redirect')->andReturn('Redirected');
		$providerName = class_basename($provider);

		$abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
		$abstractUser->token = $token;

		if (is_null($email)) {
			$abstractUser->shouldReceive('getId')
				->andReturn($provider_user_id)
				->shouldReceive('getName')
				->andReturn('Laztopaz')
				->shouldReceive('getAvatar')
				->andReturn($avatar_url);
		} else {
			$abstractUser->shouldReceive('getId')
				->andReturn($provider_user_id)
				->shouldReceive('getEmail')
				->andReturn($email)
				->shouldReceive('getName')
				->andReturn('Laztopaz')
				->shouldReceive('getAvatar')
				->andReturn($avatar_url);
		}

		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('user')->andReturn($abstractUser);

		Socialite::shouldReceive('driver')->with($provider_name)->andReturn($provider);
	}

	public function testIfEmailFromProviderEmpty()
	{
		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;

		$this->mockSocialiteFacade('', $token, 'facebook', $provider_user_id);

		$this->followingRedirects()
			->get('/auth/facebook/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.email_not_found'));
	}

	public function testIfRegistered()
	{
		Notification::fake();

		$user = factory(User::class)
			->create();

		$social_account = factory(UserSocialAccount::class)
			->create(['user_id' => $user->id]);

		$this->mockSocialiteFacade('',
			$social_account->access_token,
			$social_account->provider,
			$social_account->provider_user_id);

		$response = $this->get('/auth/' . $social_account->provider . '/callback')
			->assertRedirect(route('profile', $user->id));

		$this->assertAuthenticatedAs($user);

		Notification::assertNothingSent();
	}

	public function testIfRegisteredTokenMismatch()
	{
		Notification::fake();

		$user = factory(User::class)
			->create();

		$social_account = factory(UserSocialAccount::class)
			->create(['user_id' => $user->id]);

		$this->mockSocialiteFacade('',
			$this->faker->linuxPlatformToken,
			$social_account->provider,
			$social_account->provider_user_id);

		$response = $this->get('/auth/' . $social_account->provider . '/callback')
			->assertRedirect(route('profile', $user->id));

		$this->assertAuthenticatedAs($user);

		Notification::assertNothingSent();
	}

	public function testIfAuthTryAttach()
	{
		Notification::fake();

		$provider_user_id = $this->faker->uuid;

		$user = factory(User::class)
			->create();

		$this->mockSocialiteFacade('',
			$this->faker->linuxPlatformToken,
			'google',
			$provider_user_id,
			'http://dev.litlife.club/img/nocover4.jpeg');

		$response = $this->followingRedirects()
			->actingAs($user)
			->get('/auth/google/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.attached', ['provider' => 'google']));

		$this->assertTrue(url()->current() === route('users.social_accounts.index', $user->id));

		$this->assertAuthenticatedAs($user);

		$this->assertNotNull($user->avatar()->first());

		Notification::assertNothingSent();
	}

	public function testIfAuthTryAttachIfAvatarNotFound()
	{
		Notification::fake();

		$provider_user_id = $this->faker->uuid;

		$user = factory(User::class)
			->create();

		$this->mockSocialiteFacade('',
			$this->faker->linuxPlatformToken,
			'google',
			$provider_user_id,
			'http://test.test/test.test');

		$response = $this->followingRedirects()
			->actingAs($user)
			->get('/auth/google/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.attached', ['provider' => 'google']));

		$this->assertTrue(url()->current() === route('users.social_accounts.index', $user->id));

		$this->assertAuthenticatedAs($user);

		$this->assertNull($user->avatar()->first());

		Notification::assertNothingSent();
	}

	public function testIfNotConfirmedEmailExists()
	{
		Notification::fake();

		$user_email = factory(UserEmail::class)
			->states('not_confirmed')
			->create();

		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;
		$email = $user_email->email;

		$this->mockSocialiteFacade($email, $token, 'facebook', $provider_user_id);

		$this->get('/auth/facebook/callback')
			->assertRedirect(route('welcome'));

		$this->assertEquals($email, $user_email->email);

		$email_confirmed = UserEmail::email($email)->confirmed()->first();
		$user = $email_confirmed->user;
		$social_account = $user->social_accounts()->first();

		$this->assertNotNull($user_email->user);
		$this->assertNotEquals($user_email->user->id, $user->id);
		$this->assertNotNull($email_confirmed);
		$this->assertNotNull($user);
		$this->assertNotNull($social_account);
		$this->assertEquals($provider_user_id, $social_account->provider_user_id);
		$this->assertEquals($token, $social_account->access_token);

		$this->assertAuthenticatedAs($user);

		Notification::assertSentTo(
			$user,
			UserHasRegisteredNotification::class
		);
	}

	public function testIfNotRegisteredCantDownloadAvatar()
	{
		Notification::fake();

		$email = $this->faker->email;
		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;

		UserEmail::email($email)
			->delete();

		$this->assertNull(UserEmail::email($email)->first());

		$this->mockSocialiteFacade($email, $token, 'facebook',
			$provider_user_id, 'http://test.test/test.test');

		$this->get('/auth/facebook/callback')
			->assertRedirect(route('welcome'));

		$email = UserEmail::email($email)->first();
		$user = $email->user;
		$social_account = $user->social_accounts()->first();

		$this->assertNotNull($email);
		$this->assertNotNull($user);
		$this->assertNotNull($social_account);
		$this->assertEquals($provider_user_id, $social_account->provider_user_id);
		$this->assertEquals($token, $social_account->access_token);
		$this->assertNull($user->avatar()->first());

		$this->assertAuthenticatedAs($user);

		Notification::assertSentTo(
			$user,
			UserHasRegisteredNotification::class
		);
	}

	public function testIfUserNotFound()
	{
		Notification::fake();

		$user = factory(User::class)
			->create();

		$social_account = factory(UserSocialAccount::class)
			->create(['user_id' => $user->id]);

		$user->forceDelete();

		$this->mockSocialiteFacade('',
			$social_account->access_token,
			$social_account->provider,
			$social_account->provider_user_id);

		$response = $this->followingRedirects()
			->get('/auth/' . $social_account->provider . '/callback')
			->assertSeeText(__('user.not_found'));

		Notification::assertNothingSent();
	}

	public function testUserReference()
	{
		Notification::fake();

		$name = config('litlife.name_user_refrence_get_param');

		$this->disableCookiesEncryption($name);

		$refer_user = factory(User::class)
			->create();

		$email = $this->faker->email;
		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;

		UserEmail::email($email)
			->delete();

		$this->assertNull(UserEmail::email($email)->first());

		$this->mockSocialiteFacade($email, $token, 'facebook', $provider_user_id, config('app.url') . '/img/nocover4.jpeg');

		$this->call('get', '/auth/facebook/callback', [], [$name => $refer_user->id])
			->assertSessionHasNoErrors()
			->assertRedirect(route('welcome'));

		$email = UserEmail::email($email)->first();
		$user = $email->user;
		$social_account = $user->social_accounts()->first();

		$this->assertEquals(1, $refer_user->fresh()->data->refer_users_count);
		$this->assertEquals($user->id, $refer_user->refered_users->first()->id);

		$this->assertAuthenticatedAs($user);

		Notification::assertSentTo(
			$refer_user,
			NewReferredUserNotification::class,
			function ($notification, $channels) use ($user) {
				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$mail = $notification->toMail($user);

				$this->assertEquals(__('notification.new_refferd_user.subject'), $mail->subject);

				$this->assertEquals(__('notification.new_refferd_user.line', [
					'user_name' => $user->user_name
				]), $mail->introLines[0]);

				$this->assertEquals(__('notification.new_refferd_user.action'), $mail->actionText);

				$this->assertEquals(route('profile', $user), $mail->actionUrl);

				return $notification->user->id == $user->id;
			}
		);
	}

	public function testGoogleUndefinedIndexEmails()
	{
		$user = factory(User::class)
			->create();

		$social_account = factory(UserSocialAccount::class)
			->create(['user_id' => $user->id]);

		$this->mockSocialiteFacade('',
			$social_account->access_token,
			$social_account->provider,
			$social_account->provider_user_id);

		$response = $this->get('/auth/' . $social_account->provider . '/callback')
			->assertRedirect(route('profile', $user->id));

		$this->assertAuthenticatedAs($user);
	}

	public function testIfUserSuspendedExists()
	{
		Notification::fake();

		$user = factory(User::class)
			->states('suspended', 'with_confirmed_email')
			->create();

		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;
		$email = $user->emails()->first();

		$this->mockSocialiteFacade($email->email, $token, 'facebook', $provider_user_id);

		$this->get('/auth/facebook/callback')
			->assertRedirect(route('profile', $user->id));

		$user->refresh();

		$this->assertEquals(1, UserEmail::email($email->email)->count());

		$this->assertFalse($user->isSuspended());

		$social_account = $user->social_accounts()->first();

		$this->assertNotNull($social_account);
		$this->assertEquals($provider_user_id, $social_account->provider_user_id);
		$this->assertEquals($token, $social_account->access_token);

		$this->assertAuthenticatedAs($user);

		Notification::assertNothingSent();
	}

	public function testIfNotConfirmedEmailAndUserSuspendedExists()
	{
		Notification::fake();

		$user_with_not_confirmed_email = factory(User::class)
			->states('suspended', 'with_not_confirmed_email')
			->create();

		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;
		$user_email = $user_with_not_confirmed_email->emails()->first();
		$email = $user_email->email;

		$this->assertTrue($user_with_not_confirmed_email->isSuspended());
		$this->assertFalse($user_email->isConfirmed());

		$this->mockSocialiteFacade($email, $token, 'facebook', $provider_user_id);

		$this->get('/auth/facebook/callback')
			->assertRedirect(route('welcome'));

		$user_with_not_confirmed_email->refresh();

		$this->assertEquals($email, $user_email->email);

		$email_confirmed = UserEmail::email($email)->confirmed()->first();
		$user_with_confirmed_email = $email_confirmed->user;
		$social_account = $user_with_confirmed_email->social_accounts()->first();

		$this->assertTrue($user_with_not_confirmed_email->isSuspended());
		$this->assertFalse($user_with_confirmed_email->isSuspended());
		$this->assertNotNull($user_email->user);
		$this->assertNotEquals($user_email->user->id, $user_with_confirmed_email->id);
		$this->assertNotNull($email_confirmed);
		$this->assertNotNull($user_with_confirmed_email);
		$this->assertNotNull($social_account);
		$this->assertEquals($provider_user_id, $social_account->provider_user_id);
		$this->assertEquals($token, $social_account->access_token);

		$this->assertAuthenticatedAs($user_with_confirmed_email);

		Notification::assertSentTo(
			$user_with_confirmed_email,
			UserHasRegisteredNotification::class
		);
	}

	public function testEmailCreatedBefore()
	{
		$date = Carbon::parse('2019-03-10 00:00:00');

		$user_email = factory(UserEmail::class)
			->states('not_confirmed')
			->create(['created_at' => $date]);

		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;
		$email = $user_email->email;
		$user = $user_email->user;

		$this->assertFalse($user_email->isConfirmed());
		$this->assertTrue($user_email->isCreatedBeforeMoveToNewEngine());

		$this->mockSocialiteFacade($email, $token, 'facebook', $provider_user_id);

		$this->get('/auth/facebook/callback')
			->assertRedirect(route('profile', $user));

		$user->refresh();
		$user_email->refresh();

		$this->assertTrue($user_email->isConfirmed());

		$social_account = $user->social_accounts()->first();

		$this->assertNotNull($social_account);
		$this->assertEquals($provider_user_id, $social_account->provider_user_id);
		$this->assertEquals($token, $social_account->access_token);

		$this->assertAuthenticatedAs($user);
	}

	public function testDontChangeAvatarIfAlreadyExists()
	{
		$provider_user_id = $this->faker->uuid;

		$user = factory(User::class)
			->states('with_avatar')
			->create();

		$avatar = $user->avatar()->first();

		$this->assertNotNull($avatar);

		$this->mockSocialiteFacade('',
			$this->faker->linuxPlatformToken,
			'google',
			$provider_user_id,
			config('app.url') . '/img/nocover4.jpeg');

		$response = $this->followingRedirects()
			->actingAs($user)
			->get('/auth/google/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.attached', ['provider' => 'google']));

		$this->assertTrue(url()->current() === route('users.social_accounts.index', $user->id));

		$this->assertAuthenticatedAs($user);

		$this->assertEquals($avatar, $user->avatar()->first());
	}

	public function testFacebook400BadRequestError()
	{
		$json = <<<EOT
{
  "error": "invalid_request",
  "error_description": "Missing required parameter: code"
}
EOT;

		$request = new Request('POST', 'https://accounts.google.com/o/oauth2/token');
		$response = new Response(400, ['header' => 'value'], $json, '1.1', 'Bad Request');

		$exception = RequestException::create($request, $response);

		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('user')
			->andThrow($exception, 'message');

		Socialite::shouldReceive('driver')->with('facebook')->andReturn($provider);

		$this->followingRedirects()
			->get('/auth/facebook/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.enter_error'));
	}

	public function testVkontakte401UnauthorizedError()
	{
		$json = <<<EOT
{"error":"invalid_grant","error_description":"Code is invalid or expired."}
EOT;

		$request = new Request('POST', 'https://oauth.vk.com/access_token');
		$response = new Response(401, ['header' => 'value'], $json, '1.1', 'Unauthorized');

		$exception = RequestException::create($request, $response);

		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('user')
			->andThrow($exception, 'message');

		Socialite::shouldReceive('driver')->with('vkontakte')->andReturn($provider);

		$this->followingRedirects()
			->get('/auth/vkontakte/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.enter_error'));
	}

	public function testGoogle400BadRequestError()
	{
		$json = <<<EOT
{
  "error": "invalid_request",
  "error_description": "Missing required parameter: code"
}
EOT;

		$request = new Request('POST', 'https://accounts.google.com/o/oauth2/token');
		$response = new Response(400, ['header' => 'value'], $json, '1.1', 'Bad Request');

		$exception = RequestException::create($request, $response);

		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('user')
			->andThrow($exception, 'message');

		Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

		$this->followingRedirects()
			->get('/auth/google/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.enter_error'));
	}

	public function testUndefinedIndexEmails()
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('user')
			->andThrow(Exception::class, 'Undefined index: emails');

		Socialite::shouldReceive('driver')->with('google')->andReturn($provider);

		$this->followingRedirects()
			->get('/auth/google/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.email_not_found_allow_use_or_attach_the_mailbox_to_the_social_network'));
	}

	public function testOtherException()
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');

		$provider->shouldReceive('user')
			->andThrow(Exception::class, 'test');

		Socialite::shouldReceive('driver')
			->with('google')
			->andReturn($provider);

		$this->followingRedirects()
			->get('/auth/google/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.enter_error'));
	}

	public function testUndefinedIndexDisplayNameException()
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');

		$provider->shouldReceive('user')
			->andThrow(ErrorException::class, 'Undefined index: displayName');

		Socialite::shouldReceive('driver')
			->with('google')
			->andReturn($provider);

		$this->followingRedirects()
			->get('/auth/google/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.google_did_not_report_the_display_name_of_the_user'));
	}

	public function testInvalidJSONResponseFromVK()
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');

		$provider->shouldReceive('user')
			->andThrow(RuntimeException::class, 'Invalid JSON response from VK: {"error":{"error_code":5,"error_msg":"User authorization failed: user revoke access for this token.","request_params":[{"key":"fields","value":"id,email,first_name,last_name,screen_name,photo"},{"key":"language","value":"en"},{"key":"v","value":"5.78"},{"key":"method","value":"users.get"},{"key":"oauth","value":"1"}]}}');

		Socialite::shouldReceive('driver')
			->with('vkontakte')
			->andReturn($provider);

		$this->followingRedirects()
			->get('/auth/vkontakte/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.an_error_occurred', ['error_msg' => 'User authorization failed: user revoke access for this token.']));
	}

	public function testInvalidJSONResponseFromVKWithoutError()
	{
		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');

		$provider->shouldReceive('user')
			->andThrow(RuntimeException::class, 'Invalid JSON response from VK: {"test":{"test": ""}}');

		Socialite::shouldReceive('driver')
			->with('vkontakte')
			->andReturn($provider);

		$this->followingRedirects()
			->get('/auth/vkontakte/callback')
			->assertOk()
			->assertSeeText(__('user_social_account.enter_error'));
	}

	public function testDuplicateAccountException()
	{
		$email = uniqid() . '@' . uniqid() . '.com';
		$provider_user_id = $this->faker->uuid;
		$token = $this->faker->linuxPlatformToken;

		$this->assertNull(UserEmail::email($email)->first());

		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('redirect')->andReturn('Redirected');
		$providerName = class_basename($provider);

		$abstractUser = Mockery::mock('Laravel\Socialite\Two\User');
		$abstractUser->token = $token;

		$abstractUser->shouldReceive('getId')
			->andReturn($provider_user_id)
			->shouldReceive('getEmail')
			->andReturn($email)
			->shouldReceive('getName')
			->andReturn('Laztopaz')
			->shouldReceive('getAvatar');

		$provider = Mockery::mock('Laravel\Socialite\Contracts\Provider');
		$provider->shouldReceive('user')->andReturn($abstractUser);

		Socialite::shouldReceive('driver')
			->with('google')
			->andReturn($provider);

		$this->get('/auth/google/callback')
			->assertRedirect(route('welcome'));

		$email = UserEmail::email($email)->first();
		$user = $email->user;
		$social_account = $user->social_accounts()->first();

		$controller = new UserSocialAccountController();
		$response = $controller->createSocialAccount('google', $abstractUser, $user, true);

		$this->assertEquals(route('home'), $response->getTargetUrl());
		$this->assertEquals(302, $response->status());
	}

	public function testProviderNotFound()
	{
		$this->get('/auth/' . uniqid())
			->assertNotFound();
	}

	public function testProviders()
	{
		$this->get('/auth/google')
			->assertRedirect();

		$this->get('/auth/facebook')
			->assertRedirect();

		$this->get('/auth/vkontakte')
			->assertRedirect();
	}


}
