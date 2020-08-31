<?php

namespace Tests\Feature\User;

use App\Jobs\User\UpdateUserConfirmedMailboxCount;
use App\Notifications\EmailConfirmNotification;
use App\User;
use App\UserEmail;
use App\UserEmailToken;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserEmailTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testConfirmIfOtherEmailExists()
	{
		$user = factory(User::class)
			->states('with_not_confirmed_email')
			->create();

		$user_email = $user->emails()->first();

		$user->refreshConfirmedMailboxCount();
		$user->push();

		$user_email_token = factory(UserEmailToken::class)
			->create(['user_email_id' => $user_email->id])
			->fresh();

		//

		$user2 = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$user_email2 = $user2->emails()->first();
		$user_email2->email = $user_email->email;
		$user_email2->save();
		$user_email2->fresh();

		$user2->refreshConfirmedMailboxCount();
		$user2->setting->loginWithIdDisable();
		$user2->push();

		$user_email_token2 = factory(UserEmailToken::class)
			->create(['user_email_id' => $user_email2->id])
			->fresh();

		$this->assertNotEquals($user->id, $user2->id);
		$this->assertEquals(0, $user->fresh()->confirmed_mailbox_count);
		$this->assertEquals(1, $user2->fresh()->confirmed_mailbox_count);
		$this->assertEquals($user_email->email, $user_email2->email);
		$this->assertFalse($user2->setting->isLoginWithIdEnable());

		//

		$this->get(route('email.confirm',
			['email' => $user_email->id, 'token' => $user_email_token->token]))
			->assertOk()
			->assertSeeText(__('user_email.success_confirmed', ['email' => $user_email->email]));

		$this->assertEquals(1, $user->fresh()->confirmed_mailbox_count);
		$this->assertEquals(0, $user2->fresh()->confirmed_mailbox_count);

		$user2->refresh();
		$user_email->refresh();
		$user_email2->refresh();
		$user_email_token2->refresh();

		$this->assertTrue($user_email->isConfirmed());
		$this->assertFalse($user_email2->isConfirmed());
		$this->assertTrue($user2->setting->isLoginWithIdEnable());
	}

	public function testSendConfirmTokenIfAuth()
	{
		Notification::fake();

		$email = factory(UserEmail::class)
			->states('not_confirmed')
			->create();

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

	public function testSendConfirmTokenIfGuest()
	{
		Notification::fake();

		$email = factory(UserEmail::class)
			->states('not_confirmed')
			->create();

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

	public function testSendConfirmTokenIfEmailConfirmed()
	{
		Mail::fake();

		$email = factory(UserEmail::class)
			->states('confirmed')
			->create();

		$this->assertTrue($email->isConfirmed());

		$this->get(route('email.send_confirm_token', ['email' => $email->id]))
			->assertSeeText(__('user_email.already_confirmed'));
	}

	/*
		public function testIfOtherConfirmedEmailExists()
		{
			$email = factory(UserEmail::class)
				->states('not_confirmed')
				->create();

			$this->assertFalse($email->isConfirmed());

			$email2 = factory(UserEmail::class)
				->states('confirmed')
				->create(['email' => $email->email]);

			$this->assertTrue($email2->isConfirmed());

			$this->get(route('email.send_confirm_token', ['email' => $email->id]))
				->assertSeeText(__('user_email.error_3'));
		}
		*/

	public function testConfirm()
	{
		$email = factory(UserEmail::class)
			->states('not_confirmed')
			->create();

		$this->get(route('email.confirm', ['email' => $email->id, 'token' => $email->tokens->first()->token]))
			->assertOk()
			->assertSeeText(__('user_email.success_confirmed', ['email' => $email->email]));

		$this->assertTrue($email->fresh()->isConfirmed());
	}

	public function testConfirmTokenNotFound()
	{
		$email = factory(UserEmail::class)
			->states('not_confirmed')
			->create();

		$this->get(route('email.confirm', ['email' => $email->id, 'token' => $email->tokens->first()->token . 'wrong_token']))
			->assertOk()
			->assertSeeText(__('user_email.token_not_found'));

		$this->assertFalse($email->fresh()->isConfirmed());
	}

	public function testCreateHttp()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.emails.create', ['user' => $user]))
			->assertOk();
	}

	public function testStoreHttp()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$this->assertEquals(1, $user->emails()->count());

		$this->actingAs($user)
			->post(route('users.emails.store', ['user' => $user, 'email' => $this->faker->email]))
			->assertRedirect(route('users.emails.index', $user));

		$this->assertEquals(2, $user->emails()->count());

		$unconfirmed_email = $user->emails()->unconfirmed()->first();

		$this->assertNotNull($unconfirmed_email);
		$this->assertFalse($unconfirmed_email->isConfirmed());
	}

	public function testDeleteHttpErrorAtLeastOneEmailMustBeConfirmed()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$this->assertEquals(1, $user->emails()->count());

		$this->actingAs($user)
			->followingRedirects()
			->delete(route('users.emails.destroy', ['user' => $user, 'email' => $user->emails()->first()->id]))
			->assertSeeText(__('user_email.error_1'));
	}

	public function testDeleteHttpErrorAtLeastOneEmailMustBeForResue()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());

		$email2 = factory(UserEmail::class)
			->states('confirmed', 'noticed', 'not_rescued')
			->create(['user_id' => $user->id]);

		$this->actingAs($user)
			->followingRedirects()
			->delete(route('users.emails.destroy', ['user' => $user, 'email' => $email->id]))
			->assertSeeText(__('user_email.error_2'));
	}

	public function testDeleteHttp()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());

		$email2 = factory(UserEmail::class)
			->states('confirmed', 'noticed', 'rescued')
			->create(['user_id' => $user->id]);

		$this->assertEquals(2, $user->emails()->count());

		$this->actingAs($user)
			->delete(route('users.emails.destroy', ['user' => $user, 'email' => $email->id]))
			->assertRedirect(route('users.emails.index', $user));

		$this->assertEquals(1, $user->emails()->count());
	}

	public function testDeleteNotConfirmedIfConfirmedExists()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());
		$this->assertTrue($email->isConfirmed());
		$this->assertTrue($email->isNotice());

		$email2 = factory(UserEmail::class)
			->states('not_confirmed', 'not_rescued', 'not_noticed')
			->create(['user_id' => $user->id]);

		$this->assertEquals(2, $user->emails()->count());

		$this->actingAs($user)
			->delete(route('users.emails.destroy', ['user' => $user, 'email' => $email2->id]))
			->assertSessionHasNoErrors()
			->assertRedirect(route('users.emails.index', $user));

		$this->assertEquals(1, $user->emails()->count());
	}

	public function testDeleteHttpErrorAtLeastOneEmailMustBeForNotice()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());
		$this->assertTrue($email->isConfirmed());
		$this->assertTrue($email->isNotice());

		$email2 = factory(UserEmail::class)
			->states('confirmed', 'rescued', 'not_noticed')
			->create(['user_id' => $user->id]);

		$this->assertEquals(2, $user->emails()->count());

		$this->actingAs($user)
			->followingRedirects()
			->delete(route('users.emails.destroy', ['user' => $user, 'email' => $email->id]))
			->assertSeeText(__('user_email.you_must_have_at_least_one_mailbox_to_send_notifications'));

		$this->assertEquals(2, $user->emails()->count());
	}

	public function testUnRescue()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());
		$this->assertTrue($email->isConfirmed());
		$this->assertTrue($email->isNotice());

		$email2 = factory(UserEmail::class)
			->states('confirmed', 'rescued', 'noticed')
			->create(['user_id' => $user->id]);

		$this->assertEquals(2, $user->emails()->rescuing()->count());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('users.emails.unrescue', ['user' => $user, 'email' => $email2->id]))
			->assertSeeText(__('user_email.now_not_for_rescue', ['email' => $email2->email]));

		$this->assertTrue($email->fresh()->isRescue());
		$this->assertFalse($email2->fresh()->isRescue());
	}

	public function testUnRescureErrorAtLeastOneEmailForRescue()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());
		$this->assertTrue($email->isConfirmed());
		$this->assertTrue($email->isNotice());

		$email2 = factory(UserEmail::class)
			->states('confirmed', 'not_rescued', 'noticed')
			->create(['user_id' => $user->id]);

		$this->assertEquals(1, $user->emails()->rescuing()->count());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('users.emails.unrescue', ['user' => $user, 'email' => $email->id]))
			->assertSeeText(__('user_email.error_must_be_one_for_rescue'));

		$this->assertFalse($email2->fresh()->isRescue());
	}

	public function testUnRescureErrorNotConfirmed()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());
		$this->assertTrue($email->isConfirmed());
		$this->assertTrue($email->isNotice());

		$email2 = factory(UserEmail::class)
			->states('not_confirmed', 'rescued', 'noticed')
			->create(['user_id' => $user->id]);

		$this->actingAs($user)
			->followingRedirects()
			->get(route('users.emails.unrescue', ['user' => $user, 'email' => $email->id]))
			->assertSeeText(__('user_email.not_confirmed'));
	}

	public function testNotificationsEnable()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());
		$this->assertTrue($email->isConfirmed());
		$this->assertTrue($email->isNotice());

		$email2 = factory(UserEmail::class)
			->states('confirmed', 'rescued', 'not_noticed')
			->create(['user_id' => $user->id]);

		$this->actingAs($user)
			->followingRedirects()
			->get(route('users.emails.notifications.enable', ['user' => $user, 'email' => $email2->id]))
			->assertSeeText(__('user_email.now_for_notice', ['email' => $email2->email]));

		$this->assertFalse($email->fresh()->isNotice());
		$this->assertTrue($email2->fresh()->isNotice());
	}

	public function testShowInProfileEnable()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();
		$email->show_in_profile = false;
		$email->save();

		$this->assertFalse($email->isShowInProfile());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('users.emails.show', ['user' => $user, 'email' => $email->id]))
			->assertSeeText(__('user_email.now_showed_in_profile', ['email' => $email->email]));

		$this->assertTrue($email->fresh()->isShowInProfile());
	}

	public function testShowInProfileDisable()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isShowInProfile());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('users.emails.hide', ['user' => $user, 'email' => $email->id]))
			->assertSeeText(__('user_email.now_not_showed_in_profile', ['email' => $email->email]));

		$this->assertFalse($email->fresh()->isShowInProfile());
	}

	public function testShowInProfileEnableErrorNotConfirmed()
	{
		$user = factory(User::class)
			->states('with_not_confirmed_email')
			->create();

		$email = $user->emails()->first();
		$email->show_in_profile = false;
		$email->confirm = false;
		$email->save();

		$this->assertFalse($email->isShowInProfile());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('users.emails.show', ['user' => $user, 'email' => $email->id]))
			->assertSeeText(__('user_email.not_confirmed'));

		$this->assertFalse($email->fresh()->isShowInProfile());
	}

	public function testSendConfirmCodeIfAnotherConfirmedEmailExists()
	{
		$email_confirmed = factory(UserEmail::class)
			->states('confirmed')
			->create();

		$email_not_confirmed = factory(UserEmail::class)
			->states('not_confirmed')
			->create(['email' => $email_confirmed->email]);

		$this->actingAs($email_confirmed->user)
			->followingRedirects()
			->get(route('email.send_confirm_token', ['email' => $email_not_confirmed->id]))
			->assertOk()
			->assertDontSeeText(__('user_email.already_confirmed'))
			->assertDontSeeText(__('user_email.error_3'))
			->assertSeeText(__('user_email.confirm_url_sended', ['email' => $email_not_confirmed->email]));
	}

	public function testConfirmIfAnotherConfirmedEmailExists()
	{
		$email_confirmed = factory(UserEmail::class)
			->states('confirmed')
			->create();

		$email_not_confirmed = factory(UserEmail::class)
			->states('not_confirmed')
			->create(['email' => $email_confirmed->email]);

		$this->assertEquals($email_confirmed->email, $email_not_confirmed->email);

		$email_not_confirmed_token = $email_not_confirmed->tokens()->first();

		$this->assertNotNull($email_not_confirmed_token);
		$this->assertEquals(1, $email_confirmed->user->confirmed_mailbox_count);
		$this->assertEquals(0, $email_not_confirmed->user->confirmed_mailbox_count);

		$this->followingRedirects()
			->actingAs($email_not_confirmed->user)
			->get(route('email.confirm', [
				'email' => $email_not_confirmed->id,
				'token' => $email_not_confirmed_token->token
			]))
			->assertOk()
			->assertDontSeeText(__('common.not_found_any_confirmed_email'))
			->assertDontSeeText(__('user_email.token_not_found'))
			->assertSeeText(__('user_email.success_confirmed', ['email' => $email_not_confirmed->email]));

		$email_not_confirmed->refresh();
		$email_confirmed->refresh();

		$this->assertTrue($email_not_confirmed->isConfirmed());
		$this->assertFalse($email_confirmed->isConfirmed());
		$this->assertEquals(1, $email_confirmed->user->emails()->whereEmail($email_confirmed->email)->count());

		$this->assertEquals(2, UserEmail::whereEmail($email_confirmed->email)->count());
	}

	public function testRefreshConfirmedMailboxCount()
	{
		$email = factory(UserEmail::class)
			->states('confirmed')
			->create();

		$user = $email->user;

		$user->refreshConfirmedMailboxCount();
		$user->save();
		$user->refresh();

		$this->assertEquals(1, $user->confirmed_mailbox_count);

		$email->confirm = false;
		$email->save();

		$user->refreshConfirmedMailboxCount();
		$user->save();
		$user->refresh();
		$email->refresh();

		$this->assertFalse($email->isConfirmed());

		$this->assertEquals(0, $user->confirmed_mailbox_count);

		$email->confirm = true;
		$email->save();

		$user->refreshConfirmedMailboxCount();
		$user->save();
		$user->refresh();
		$email->refresh();

		$this->assertTrue($email->isConfirmed());

		$this->assertEquals(1, $user->confirmed_mailbox_count);
	}

	public function testSeeYouNeedAtLeastOneConfirmedEmail()
	{
		$email = factory(UserEmail::class)
			->states('not_confirmed')
			->create();

		$user = $email->user;

		$this->actingAs($user)
			->get(route('profile', $user))
			->assertOk()
			->assertSeeText(__('common.not_found_any_confirmed_email'))
			->assertSeeText(__('common.please_confirm_email'))
			->assertSeeText(__('common.go_to_my_mailboxes'));

		$this->actingAs($user)
			->get(route('users.emails.index', $user))
			->assertOk()
			->assertDontSeeText(__('common.not_found_any_confirmed_email'))
			->assertDontSeeText(__('common.please_confirm_email'))
			->assertDontSeeText(__('common.go_to_my_mailboxes'));
	}

	public function testIsCreatedBeforeMoveToNewEngine()
	{
		$email = factory(UserEmail::class)
			->create();

		$email->created_at = '2020-03-12 00:00:01';

		$this->assertFalse($email->isCreatedBeforeMoveToNewEngine());

		$email->created_at = '2019-03-13 00:00:00';

		$this->assertFalse($email->isCreatedBeforeMoveToNewEngine());

		$email->created_at = '2019-03-11 00:00:00';

		$this->assertTrue($email->isCreatedBeforeMoveToNewEngine());

		$email->created_at = '2018-02-01 00:00:00';

		$this->assertTrue($email->isCreatedBeforeMoveToNewEngine());

		$email->created_at = '2000-01-03 12:00:00';

		$this->assertTrue($email->isCreatedBeforeMoveToNewEngine());

		$email->created_at = '2019-03-11 23:59:59';

		$this->assertTrue($email->isCreatedBeforeMoveToNewEngine());
	}

	public function testCreatedBeforeMoveToNewEngineScope()
	{
		$email = factory(UserEmail::class)
			->create();

		$email->created_at = '2019-03-13 00:00:00';
		$email->save();

		$this->assertEquals(0, UserEmail::whereEmail($email->email)->createdBeforeMoveToNewEngine()->count());

		$email->created_at = '2019-03-11 00:00:00';
		$email->save();

		$this->assertEquals(1, UserEmail::whereEmail($email->email)->createdBeforeMoveToNewEngine()->count());
	}

	public function testCreateIfSameEmailInOtherUserAccountExists()
	{
		$email = factory(UserEmail::class)
			->states('confirmed')
			->create();

		$user = factory(User::class)
			->states('without_email')
			->create();

		$response = $this->actingAs($user)
			->post(route('users.emails.store', ['user' => $user]), [
				'email' => $email->email
			])
			->assertRedirect();
		if (session('errors')) dump(session('errors'));
		$response->assertSessionHasNoErrors();

		$email->refresh();

		$this->assertTrue($email->isConfirmed());
		$this->assertEquals(1, $user->emails()->count());
		$this->assertEquals(1, $user->emails()->whereEmail($email->email)->count());

		$email2 = $user->emails()->whereEmail($email->email)->first();

		$this->assertFalse($email2->isConfirmed());
		$this->assertFalse($email2->isRescue());
		$this->assertFalse($email2->isNotice());
	}

	public function testCreateIfEmailAlreadyAddedToUserAccount()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertNotNull($email);

		$response = $this->actingAs($user)
			->post(route('users.emails.store', ['user' => $user]), [
				'email' => $email->email
			]);
		//dump(session('errors'));
		$this->assertContains(__('user_email.you_have_already_added_such_a_mailbox'), session('errors')->all());

		$response = $this->actingAs($user)
			->followingRedirects()
			->post(route('users.emails.store', ['user' => $user]), [
				'email' => $email->email
			])
			->assertOk()
			->assertSeeText(__('user_email.you_have_already_added_such_a_mailbox'));
	}

	public function testDontEnableLoginByIDIfThereAreConfirmedMailboxesExists()
	{
		$email_confirmed = factory(UserEmail::class)
			->states('confirmed')
			->create();

		$email_confirmed2 = factory(UserEmail::class)
			->states('confirmed')
			->create(['user_id' => $email_confirmed->user->id]);

		$email_not_confirmed = factory(UserEmail::class)
			->states('not_confirmed')
			->create(['email' => $email_confirmed->email]);

		$this->assertFalse($email_confirmed->user->setting->isLoginWithIdEnable());

		$email_not_confirmed_token = $email_not_confirmed->tokens()->first();

		$this->followingRedirects()
			->actingAs($email_not_confirmed->user)
			->get(route('email.confirm', [
				'email' => $email_not_confirmed->id,
				'token' => $email_not_confirmed_token->token
			]))
			->assertOk()
			->assertSeeText(__('user_email.success_confirmed', ['email' => $email_not_confirmed->email]));

		$email_not_confirmed->refresh();
		$email_confirmed->refresh();

		$this->assertFalse($email_confirmed->user->setting->isLoginWithIdEnable());

		$this->assertTrue($email_not_confirmed->isConfirmed());
		$this->assertFalse($email_confirmed->isConfirmed());
	}

	public function testSeeEmailIfShowInProfile()
	{
		$email = factory(UserEmail::class)
			->states('show_in_profile')
			->create();

		$user = $email->user;

		$this->assertTrue($email->isShowInProfile());

		$this->get(route('profile', $user))
			->assertOk()
			->assertSeeText($email->email);
	}

	public function testSeeEmailIfDontShowInProfile()
	{
		$email = factory(UserEmail::class)
			->states('dont_show_in_profile')
			->create();

		$user = $email->user;

		$this->assertFalse($email->isShowInProfile());

		$this->get(route('profile', $user))
			->assertOk()
			->assertDontSeeText($email->email);
	}

	public function testSendConfirmCodeToRightEmail()
	{
		Notification::fake();

		$user = factory(User::class)
			->create();

		$email = factory(UserEmail::class)
			->states('confirmed')
			->create(['user_id' => $user->id]);

		$email2 = factory(UserEmail::class)
			->states('not_confirmed')
			->create(['user_id' => $user->id]);

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
}
