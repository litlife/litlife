<?php

namespace Tests\Feature\User\Email;

use App\Notifications\EmailConfirmNotification;
use App\User;
use App\UserEmail;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserEmailSendConfirmTokenTest extends TestCase
{
	public function testIfAuth()
	{
		Notification::fake();

		$email = UserEmail::factory()->not_confirmed()->create();

		$this->actingAs($email->user)
			->followingRedirects()
			->get(route('email.send_confirm_token', ['email' => $email->id]))
			->assertSeeText(__('user_email.confirm_url_sended', ['email' => $email->email]));

		$token = $email->tokens()->orderBy('id', 'asc')->get()[1];

		$this->assertNotNull($token);

		Notification::assertSentTo(
			new AnonymousNotifiable(),
			EmailConfirmNotification::class,
			function ($notification, $channels, $notifiable) use ($token, $email) {
				$this->assertContains('mail', $channels);

				$mail = $notification->toMail($token);

				$this->assertEquals($notifiable->routes['mail'], $email->email);

				$this->assertEquals($notification->token->email->id, $token->email->id);
				$this->assertEquals($notification->token->token, $token->token);

				$this->assertEquals(__('notification.email_confirm.subject'), $mail->subject);
				$this->assertEquals(__('notification.greeting') . ', ' . $token->email->user->userName . '!', $mail->greeting);
				$this->assertEquals(__('notification.email_confirm.line', ['email' => $token->email->email]), $mail->introLines[0]);
				$this->assertEquals(__('notification.email_confirm.action'), $mail->actionText);
				$this->assertEquals(route('email.confirm', ['email' => $token->email, 'token' => $token->token]), $mail->actionUrl);

				return $notification->token->id == $token->id;
			}
		);
	}

	public function testIfGuest()
	{
		Notification::fake();

		$email = UserEmail::factory()->not_confirmed()->create();

		$this->get(route('email.send_confirm_token', ['email' => $email->id]))
			->assertSeeText(__('user_email.confirm_url_sended', ['email' => $email->email]));

		$token = $email->tokens()->orderBy('id', 'asc')->get()[1];

		$this->assertNotNull($token);

		Notification::assertSentTo(
			new AnonymousNotifiable(),
			EmailConfirmNotification::class,
			function ($notification, $channels, $notifiable) use ($token, $email) {
				$this->assertContains('mail', $channels);

				$mail = $notification->toMail($token);

				$this->assertEquals($notifiable->routes['mail'], $email->email);

				$this->assertEquals($notification->token->email->id, $token->email->id);
				$this->assertEquals($notification->token->token, $token->token);

				$this->assertEquals(__('notification.email_confirm.subject'), $mail->subject);
				$this->assertEquals(__('notification.greeting') . ', ' . $token->email->user->userName . '!', $mail->greeting);
				$this->assertEquals(__('notification.email_confirm.line', ['email' => $token->email->email]), $mail->introLines[0]);
				$this->assertEquals(__('notification.email_confirm.action'), $mail->actionText);
				$this->assertEquals(route('email.confirm', ['email' => $token->email, 'token' => $token->token]), $mail->actionUrl);

				return $notification->token->id == $token->id;
			}
		);
	}

	public function testErrorEmailConfirmed()
	{
		Mail::fake();

		$email = UserEmail::factory()->confirmed()->create();

		$this->assertTrue($email->isConfirmed());

		$this->get(route('email.send_confirm_token', ['email' => $email->id]))
			->assertSeeText(__('user_email.already_confirmed'));
	}

	public function testIfAnotherConfirmedEmailExists()
	{
		$email_confirmed = UserEmail::factory()->confirmed()->create();

		$email_not_confirmed = UserEmail::factory()->not_confirmed()->create();

		$this->actingAs($email_confirmed->user)
			->followingRedirects()
			->get(route('email.send_confirm_token', ['email' => $email_not_confirmed->id]))
			->assertOk()
			->assertDontSeeText(__('user_email.already_confirmed'))
			->assertDontSeeText(__('user_email.error_3'))
			->assertSeeText(__('user_email.confirm_url_sended', ['email' => $email_not_confirmed->email]));
	}

	public function testSendConfirmCodeToRightEmail()
	{
		Notification::fake();

		$user = User::factory()->create();

		$email = UserEmail::factory()->confirmed()->create();

		$email2 = UserEmail::factory()->not_confirmed()->create();

		$this->actingAs($user)
			->followingRedirects()
			->get(route('email.send_confirm_token', ['email' => $email2->id]))
			->assertSeeText(__('user_email.confirm_url_sended', ['email' => $email2->email]));

		$token = $email2->tokens()->orderBy('id', 'asc')->get()[1];

		$this->assertNotNull($token);

		Notification::assertSentTo(
			new AnonymousNotifiable(),
			EmailConfirmNotification::class,
			function ($notification, $channels, $notifiable) use ($token, $email, $email2) {
				$this->assertContains('mail', $channels);

				$mail = $notification->toMail($token);

				$this->assertEquals($notifiable->routes['mail'], $email2->email);

				$this->assertNotEquals($notification->token->email->email, $email->email);
				$this->assertEquals($notification->token->email->email, $email2->email);

				$this->assertEquals($notification->token->email->email, $token->email->email);
				$this->assertEquals($notification->token->email->id, $token->email->id);
				$this->assertEquals($notification->token->token, $token->token);

				$this->assertEquals(__('notification.email_confirm.subject'), $mail->subject);
				$this->assertEquals(__('notification.greeting') . ', ' . $token->email->user->userName . '!', $mail->greeting);
				$this->assertEquals(__('notification.email_confirm.line', ['email' => $token->email->email]), $mail->introLines[0]);
				$this->assertEquals(__('notification.email_confirm.action'), $mail->actionText);
				$this->assertEquals(route('email.confirm', ['email' => $token->email, 'token' => $token->token]), $mail->actionUrl);

				return $notification->token->id == $token->id;
			}
		);
	}

	public function testErrorIfMailboxInvalidFormat()
	{
		$email = factory(UserEmail::class)
			->states('not_confirmed')
			->create([
				'email' => 'test.@test.com',
				'is_valid' => false
			]);

		$this->actingAs($email->user)
			->followingRedirects()
			->get(route('email.send_confirm_token', ['email' => $email->id]))
			->assertOk()
			->assertSeeText(__('The address of the mailbox has an invalid format. Please add a mailbox with the correct address.'));
	}
	/*
		public function testIfOtherConfirmedEmailExists()
		{
			$email = UserEmail::factory()->not_confirmed()->create();

			$this->assertFalse($email->isConfirmed());

			$email2 = UserEmail::factory()->confirmed()->create();

			$this->assertTrue($email2->isConfirmed());

			$this->get(route('email.send_confirm_token', ['email' => $email->id]))
				->assertSeeText(__('user_email.error_3'));
		}
		*/
}
