<?php

namespace Tests\Feature\User;

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use App\Notifications\PasswordResetNotification;
use App\PasswordReset;
use App\User;
use App\UserEmail;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserRecoverPasswordTest extends TestCase
{
	public function testRequestFormHttp()
	{
		$this->get(route('password.request'))
			->assertOk()
			->assertSeeText(__('user_email.email'))
			->assertSeeText(__('auth.send_link_to_password_restore'));
	}

	public function testPasswordResetHttp()
	{
		Notification::fake();

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$user->suspend();

		$email = $user->emails()->first();

		$user->refresh();
		$this->assertFalse($user->isActive());

		//$this->preventCaptchaValidation();

		$response = $this->post(route('password.email'), [
			'g-recaptcha-response' => '1',
			'email' => $email->email
		]);
		if (session('errors')) dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$password_reset = PasswordReset::whereEmail($email->email)->first();

		$this->assertNotNull($password_reset);

		Notification::assertSentTo(
			$password_reset->user,
			PasswordResetNotification::class,
			function ($notification, $channels) use ($password_reset) {
				$this->assertContains('mail', $channels);

				$mail = $notification->toMail($password_reset);

				$this->assertEquals($notification->passwordReset->email, $password_reset->email);
				$this->assertEquals($notification->passwordReset->token, $password_reset->token);

				$this->assertEquals(__('notification.password_reset.subject'), $mail->subject);
				$this->assertEquals(__('notification.password_reset.line'), $mail->introLines[0]);
				$this->assertEquals(__('notification.password_reset.action'), $mail->actionText);
				$this->assertEquals(route('password.reset_form', $password_reset->token), $mail->actionUrl);

				return $notification->passwordReset->id == $password_reset->id;
			}
		);
	}

	public function testEnterNewPasswordHttp()
	{
		$passwordReset = factory(PasswordReset::class)
			->states('with_user_with_confirmed_email')
			->create();

		$user = $passwordReset->user;
		$user->suspend();
		$user->refresh();
		$this->assertFalse($user->isActive());

		$this->get(route('password.reset_form', ['token' => $passwordReset->token]))
			->assertOk();

		$password = $this->fakePassword();

		$this->post(route('password.reset', ['token' => $passwordReset->token]),
			[
				'token' => $passwordReset->token,
				'password' => $password,
				'password_confirmation' => $password
			])
			->assertSessionHasNoErrors()
			->assertRedirect(route('profile', $user));

		$passwordReset->refresh();

		$this->assertTrue($passwordReset->isUsed());

		$this->assertAuthenticatedAs($user);

		$user->refresh();
		$this->assertTrue($user->isActive());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function fakePassword(): string
	{
		return 'Abc' . rand(1000, 20000);
	}

	public function testPasswordsNotEquals()
	{
		$password_reset = factory(PasswordReset::class)->states('with_user_with_confirmed_email')->create();

		$this->get(route('password.reset_form', ['token' => $password_reset->token]))
			->assertOk();

		$this->post(route('password.reset', ['token' => $password_reset->token]),
			[
				'token' => $password_reset->token,
				'password' => Faker::create()->password,
				'password_confirmation' => Faker::create()->password
			])
			//->assertSessionHasNoErrors();
			->assertSessionHasErrors(['password' => __('validation.confirmed', ['attribute' => __('user.password')])], null, 'password_reset');
	}

	public function testWrongToken()
	{
		$password_reset = factory(PasswordReset::class)->states('with_user_with_confirmed_email')->create();

		$wrong_token = uniqid();

		$this->get(route('password.reset_form', ['token' => $wrong_token]))
			->assertRedirect(route('password.request'));

		$this->assertSessionHasErrors(__('password_reset.this_password_reset_link_is_outdated_or_entered_incorrectly'), 'email');

		$password = $this->fakePassword();

		$this->post(route('password.reset', ['token' => $wrong_token]),
			[
				'token' => $wrong_token,
				'password' => $password,
				'password_confirmation' => $password
			])
			->assertRedirect(route('password.request'));

		$this->assertSessionHasErrors(__('password_reset.this_password_reset_link_is_outdated_or_entered_incorrectly'), 'email');
	}

	public function testRedirectIfPasswordResetIsUsed()
	{
		$password_reset = factory(PasswordReset::class)
			->states('with_user_with_confirmed_email', 'used')
			->create();

		$this->assertTrue($password_reset->isUsed());

		$response = $this->get(route('password.reset_form', ['token' => $password_reset->token]))
			->assertRedirect(route('password.request'));

		$this->assertSessionHasErrors(__('password_reset.this_password_reset_link_has_already_been_used'), 'email');

		$this->followingRedirects()
			->get(route('password.reset_form', ['token' => $password_reset->token]))
			->assertSeeText(__('password_reset.this_password_reset_link_has_already_been_used'));
	}

	public function testFrequentPassword()
	{
		$password_reset = factory(PasswordReset::class)
			->states('with_user_with_confirmed_email')
			->create();

		$password = $this->fakePassword();

		$user = factory(User::class, config('auth.max_frequent_password_count'))
			->create(['password' => $password]);

		$this->assertEquals(config('auth.max_frequent_password_count'), User::wherePassword($password)->count());

		$response = $this->post(route('password.reset', ['token' => $password_reset->token]),
			[
				'token' => $password_reset->token,
				'password' => $password,
				'password_confirmation' => $password
			]);

		$response->assertSessionHasErrors(['password' => __('password.frequent')], null, 'password_reset');
	}

	public function testSetNewPasswordUserNotFound()
	{
		$password_reset = factory(PasswordReset::class)
			->states('with_user_with_confirmed_email')
			->create();

		$user = $password_reset->user;

		$user->delete();

		$this->assertTrue($user->trashed());

		$password = $this->fakePassword();

		$this->followingRedirects()
			->post(route('password.reset', ['token' => $user->password_resets->first()->token]),
				[
					'token' => $user->password_resets->first()->token,
					'password' => $password,
					'password_confirmation' => $password
				])
			->assertOk()
			->assertSeeText(__('user_email.error_user_not_found'));
	}

	public function testUserNotFound()
	{
		$email = factory(UserEmail::class)
			->states(['confirmed', 'rescued'])
			->create();

		$email->user->delete();

		$this->followingRedirects()
			->post(route('password.email'),
				[
					'g-recaptcha-response' => '1',
					'email' => $email->email
				])
			->assertOk()
			->assertSeeText(__('user_email.error_user_not_found'));

		$password_reset = PasswordReset::where('email', $email->email)->first();

		$this->assertNull($password_reset);
	}

	public function testEmailNotEnabledForRescue()
	{
		$email = factory(UserEmail::class)
			->states(['confirmed', 'not_rescued'])
			->create();

		$this->followingRedirects()
			->post(route('password.email'),
				[
					'g-recaptcha-response' => '1',
					'email' => $email->email
				])
			->assertOk()
			->assertSeeText(__('user_email.mailbox_not_enabled_for_rescue'));
	}

	public function test_allow_recover_password_with_an_unconfirmed_mailbox_if_the_mailbox_is_added_before_moving_to_a_new_engine()
	{
		Mail::fake();

		$user = factory(User::class)
			->create();

		$not_confirmed_email = factory(UserEmail::class)
			->states('not_confirmed', 'created_before_move_to_new_engine')
			->create([
				'user_id' => $user->id,
			]);

		$this->followingRedirects()
			->post(route('password.email'),
				[
					'g-recaptcha-response' => '1',
					'email' => $not_confirmed_email->email
				])
			->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('auth.link_to_password_restore_sended', ['email' => $not_confirmed_email->email]));

		$password_reset = PasswordReset::where('email', $not_confirmed_email->email)->first();

		$this->assertNotNull($password_reset);
	}

	public function testEmailNotFoundIfNotConfirmed()
	{
		$user = factory(User::class)
			->create();

		$not_confirmed_email = factory(UserEmail::class)
			->states('not_confirmed')
			->create([
				'user_id' => $user->id,
			]);

		$this->followingRedirects()
			->post(route('password.email'),
				[
					'g-recaptcha-response' => '1',
					'email' => $not_confirmed_email->email
				])
			->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('auth.email_not_found', ['email' => $not_confirmed_email->email]));

	}

	public function testConfirmEmailAfterPasswordRecoverIfEmailNotConfirmed()
	{
		$password_reset = factory(PasswordReset::class)
			->states('with_user_with_confirmed_email')
			->create();

		$user = $password_reset->user;

		$user->emails()->delete();

		$not_confirmed_email = factory(UserEmail::class)
			->states('not_confirmed')
			->create([
				'user_id' => $user->id,
				'email' => $password_reset->email
			]);

		$not_confirmed_email_other1 = factory(UserEmail::class)
			->states('not_confirmed')
			->create([
				'email' => $password_reset->email
			]);

		$not_confirmed_email_other2 = factory(UserEmail::class)
			->states('not_confirmed')
			->create([
				'user_id' => $user->id
			]);

		$password = 'Abc' . rand(1000, 999999);

		$this->post(route('password.reset', ['token' => $password_reset->token]),
			[
				'token' => $password_reset->token,
				'password' => $password,
				'password_confirmation' => $password
			])
			->assertSessionHasNoErrors()
			->assertRedirect(route('profile', $user));

		$password_reset->refresh();
		$not_confirmed_email->refresh();

		$this->assertNotNull($password_reset->used_at);

		$this->assertAuthenticatedAs($user);

		$this->assertTrue($not_confirmed_email->isConfirmed());

		$not_confirmed_email_other1->refresh();
		$not_confirmed_email_other2->refresh();

		$this->assertFalse($not_confirmed_email_other1->isConfirmed());
		$this->assertFalse($not_confirmed_email_other2->isConfirmed());
	}

	public function testDeleteExpiredNotUsedTokens()
	{
		$days = 2;

		config(['litlife.number_of_days_after_which_to_delete_unused_password_recovery_tokens' => $days]);

		$password_reset_outdated = factory(PasswordReset::class)->create(['created_at' => now()->subDays($days)->subMinute()]);
		$password_reset_not_outdated = factory(PasswordReset::class)->create(['created_at' => now()->subDays($days)->addMinute()]);

		Artisan::call('password_resets:delete_expired');

		$this->assertTrue($password_reset_outdated->fresh()->trashed());
		$this->assertFalse($password_reset_not_outdated->fresh()->trashed());
	}

	protected function setUp(): void
	{
		parent::setUp();

		NoCaptcha::shouldReceive('verifyResponse')
			->zeroOrMoreTimes()
			->andReturn(true);

		NoCaptcha::shouldReceive('display')
			->zeroOrMoreTimes()
			->andReturn('<input type="hidden" name="g-recaptcha-response" value="1" />');

		NoCaptcha::shouldReceive('renderJs')
			->zeroOrMoreTimes()
			->andReturn('');
	}
}
