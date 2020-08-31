<?php

namespace Tests\Feature\User;

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use App\Enums\UserGroupEnum;
use App\Invitation;
use App\Notifications\InvitationNotification;
use App\Notifications\UserHasRegisteredNotification;
use App\User;
use App\UserEmail;
use App\UserGroup;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserInvitationTest extends TestCase
{
	public function testInvitationHttp()
	{
		$this->get(route('invitation'))
			->assertOk();
	}

	public function testSendInvitationHttp()
	{
		Notification::fake();

		$email = $this->faker->email;
		$password = $this->getPassword();

		$this->preventCaptchaValidation();

		$this->post(route('invitation.store'), ['g-recaptcha-response' => '1', 'email' => $email])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$invitation = Invitation::whereEmail($email)->first();

		$this->assertNotNull($invitation);

		Notification::assertSentTo(
			new AnonymousNotifiable,
			InvitationNotification::class,
			function ($notification, $channels) use ($invitation) {
				$this->assertContains('mail', $channels);

				$mail = $notification->toMail($invitation);

				$this->assertEquals($notification->invitation->email, $invitation->email);
				$this->assertEquals($notification->invitation->token, $invitation->token);

				$this->assertEquals(__('notification.invitation.subject'), $mail->subject);
				$this->assertEquals(__('notification.invitation.line'), $mail->introLines[0]);
				$this->assertEquals(__('notification.invitation.action'), $mail->actionText);
				$this->assertEquals(route('users.registration', $invitation->token), $mail->actionUrl);

				return $notification->invitation->id == $invitation->id;
			}
		);
	}

	public function getPassword()
	{
		return 'asV' . rand(1000, 2000);
	}

	public function testRightTokenHttp()
	{
		$invitation = factory(Invitation::class)
			->create();

		$this->get(route('users.registration', ['token' => $invitation->token]))
			->assertOk();
	}

	public function testWrongTokenHttp()
	{
		$this->get(route('users.registration', ['token' => '123']))
			->assertRedirect(route('invitation'));

		$errors = pos(session('errors')->getBag('invitation')->toArray());
		$this->assertContains(__('invitation.invitation_not_found_or_expired_please_send_a_new_invitation'), $errors);

		$this->followingRedirects()
			->get(route('users.registration', ['token' => '123']))
			->assertSeeText(__('invitation.invitation_not_found_or_expired_please_send_a_new_invitation'));
	}

	public function testStoreNewUserHttp()
	{
		Notification::fake();

		$invitation = factory(Invitation::class)
			->create();

		$password = $this->getPassword();

		$nick = uniqid();

		$response = $this->post(route('users.store', ['token' => $invitation->token]),
			[
				'nick' => $nick,
				'first_name' => $this->faker->firstName,
				'last_name' => $this->faker->lastName,
				'middle_name' => '',
				'gender' => 'male',
				'password' => $password,
				'password_confirmation' => $password,
				'name_show_type' => 'FullLastNameFirstName'
			]);

		if (session('errors'))
			dump(session('errors'));

		$response->assertSessionHasNoErrors()
			->assertRedirect(route('welcome'));

		$this->assertAuthenticated();

		$user = User::where('nick', $nick)->firstOrFail();
		$email = $user->emails()->first();

		$this->assertAuthenticatedAs($user);

		$this->assertEquals(UserGroupEnum::User, $user->group->key);

		Notification::assertSentTo(
			$user,
			UserHasRegisteredNotification::class,
			function ($notification, $channels) use ($user, $email) {

				$this->assertContains('mail', $channels);

				$mail = $notification->toMail($user);

				$this->assertEquals(__('notification.user_has_registered.subject'), $mail->subject);

				$this->assertEquals(__('notification.user_has_registered.line'), $mail->introLines[0]);

				$this->assertEquals(4, count($mail->introLines));

				$this->assertEquals(__('notification.user_has_registered.line2'), $mail->introLines[1]);

				$this->assertEquals(__('notification.user_has_registered.line3', [
					'email' => $email->email
				]), $mail->introLines[2]);

				$this->assertEquals(__('notification.user_has_registered.line4', [
					'password' => __('password.your_entered_password')
				]), $mail->introLines[3]);

				$this->assertEquals(route('profile', ['user' => $user]), $mail->actionUrl);
				$this->assertEquals(__('notification.user_has_registered.action'), $mail->actionText);
				/*
								$result = Auth::guard()->attempt([
									'login' => $email->email,
									'password' => $notification->password
								]);

								$this->assertTrue($result);
				*/
				return $notification->user->id == $user->id;
			}
		);
	}

	public function testSameEmailExists()
	{
		Mail::fake();

		$response = $this->get(route('invitation'));
		$response->assertOk();

		$email = factory(UserEmail::class)
			->create(['confirm' => true]);

		// prevent validation error on captcha
		NoCaptcha::shouldReceive('verifyResponse')
			->once()
			->andReturn(true);

		$response = $this->post(route('invitation.store'),
			[
				'g-recaptcha-response' => '1',
				'email' => $email->email
			]
		);

		$response->assertRedirect()
			->assertSessionHasErrors(['email'], __('validation.user_email_unique'), 'invitation');
	}

	public function testSameEmailUnconfirmedExists()
	{
		Mail::fake();

		$response = $this->get(route('invitation'));
		$response->assertOk();

		$email = factory(UserEmail::class)
			->create([
				'confirm' => false,
			]);

		$emailbox = $email->email;

		// prevent validation error on captcha
		NoCaptcha::shouldReceive('verifyResponse')
			->once()
			->andReturn(true);

		$response = $this->post(route('invitation.store'),
			[
				'g-recaptcha-response' => '1',
				'email' => $emailbox
			]
		)->assertRedirect();
		var_dump(session('errors'));
		$response->assertSessionHasNoErrors();

		$invitation = Invitation::latest()->limit(1)->first();

		$this->assertNotNull($invitation);
		$this->assertEquals($emailbox, $invitation->email);

		$email->confirm = true;
		$email->save();

		$password = $this->getPassword();

		$user = factory(User::class)
			->make();

		$response = $this->post(route('users.store', ['token' => $invitation->token]),
			[
				'nick' => $user->nick,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
				'middle_name' => $user->middle_name,
				'gender' => 'male',
				'password' => $password,
				'password_confirmation' => $password,
				'name_show_type' => 'FullLastNameFirstName'
			]);
		if (!empty(session('errors'))) dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('invitation'));
	}

	public function testNewUserSameNickExists()
	{
		$nick = $this->faker->userName;

		$nick_uppercase = mb_strtoupper($nick);
		$nick_lowercase = mb_strtolower($nick);

		$invitation = factory(Invitation::class)
			->create();

		$user = factory(User::class)
			->create(['nick' => $nick_uppercase]);

		$user_filled_data = factory(User::class)
			->make(['nick' => $nick_lowercase]);

		$post = $user_filled_data->toArray();
		$password = $this->getPassword();
		$post['password'] = $password;
		$post['password_confirmation'] = $password;

		$this->post(route('users.store', ['token' => $invitation->token]), $post)
			->assertSessionHasErrors(['nick' => __('validation.user_nick_unique')], null, 'registration')
			->assertRedirect();
	}

	public function testNewUser()
	{
		$invitation = factory(Invitation::class)
			->create();

		$user_filled_data = factory(User::class)
			->make();

		$post = $user_filled_data->toArray();
		$password = $this->getPassword();
		$post['password'] = $password;
		$post['password_confirmation'] = $password;

		$response = $this->post(route('users.store', ['token' => $invitation->token]), $post)
			->assertRedirect();
		if (!empty(session('errors'))) dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$user_email = UserEmail::email($invitation->email)
			->first();

		$user = $user_email->user;

		$this->assertNotNull($user);
		$this->assertNotNull($user_email->user->group);
		$this->assertEquals(UserGroup::where('key', UserGroupEnum::User)->firstOrFail()->id, $user_email->user->group->id);
	}

	public function testEmptyPassword()
	{
		$invitation = factory(Invitation::class)
			->create();

		$user_filled_data = factory(User::class)
			->make();

		$post = $user_filled_data->toArray();
		$password = $this->getPassword();
		$post['password'] = '';
		$post['password_confirmation'] = '';

		$response = $this->post(route('users.store', ['token' => $invitation->token]), $post)
			->assertRedirect();
		//dump(session('errors'));
		$response->assertSessionHasErrors(['password' => __('validation.required', ['attribute' => __('user.password')])],
			null, 'registration');
	}

	public function testFrequentPassword()
	{
		config(['auth.max_frequent_password_count' => 1]);

		$invitation = factory(Invitation::class)
			->create();

		$user_filled_data = factory(User::class)
			->make();

		$post = $user_filled_data->toArray();
		$password = $this->getPassword();
		$post['password'] = $password;
		$post['password_confirmation'] = $password;

		$user = factory(User::class, config('auth.max_frequent_password_count'))
			->create(['password' => $password]);

		$this->assertEquals(config('auth.max_frequent_password_count'), User::wherePassword($password)->count());

		$response = $this->post(route('users.store', ['token' => $invitation->token]), $post)
			->assertRedirect();
		//dump(session('errors'));
		$response->assertSessionHasErrors(['password' => __('password.frequent')],
			null, 'registration');
	}

	public function testStoreNewUserDate()
	{
		$invitation = factory(Invitation::class)
			->create();

		$password = $this->getPassword();

		$response = $this->post(route('users.store', ['token' => $invitation->token]),
			[
				'nick' => uniqid(),
				'first_name' => $this->faker->firstName,
				'last_name' => $this->faker->lastName,
				'middle_name' => '',
				'gender' => 'male',
				'password' => $password,
				'password_confirmation' => $password,
				'name_show_type' => 'FullLastNameFirstName',
				'born_day' => '12',
				'born_month' => '12',
				'born_year' => '1990'
			]);
		if (session('errors')) var_dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('welcome'));

		$user = auth()->user();

		$this->assertEquals('1990-12-12', $user->born_date->format('Y-m-d'));
	}

	public function testDenyInvitationIfUnconfirmedEmailExistsAndCreatedBeforeMoveToNewEngine()
	{
		$email = factory(UserEmail::class)
			->states('not_confirmed', 'created_before_move_to_new_engine')
			->create();

		NoCaptcha::shouldReceive('verifyResponse')
			->once()
			->andReturn(true);

		$response = $this->post(route('invitation.store'),
			[
				'g-recaptcha-response' => '1',
				'email' => $email->email
			]
		)->assertRedirect();
		//dump(session('errors'));
		$response->assertSessionHasErrorsIn('invitation', ['email' => __('validation.user_email_unique')]);
	}

	public function testDenyInvitationIfConfirmedEmailExistsAndCreatedAfterMoveToNewEngine()
	{
		$email = factory(UserEmail::class)
			->states('confirmed')
			->create();

		NoCaptcha::shouldReceive('verifyResponse')
			->once()
			->andReturn(true);

		$response = $this->post(route('invitation.store'),
			[
				'g-recaptcha-response' => '1',
				'email' => $email->email
			]
		)->assertRedirect();
		//dump(session('errors'));
		$response->assertSessionHasErrorsIn('invitation', ['email' => __('validation.user_email_unique')]);
	}
}
