<?php

namespace Tests\Feature\User\Email;

use App\User;
use App\UserEmail;
use App\UserEmailToken;
use Tests\TestCase;

class UserEmailConfirmTest extends TestCase
{
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

	public function testIfTokenNotFound()
	{
		$email = factory(UserEmail::class)
			->states('not_confirmed')
			->create();

		$this->get(route('email.confirm', ['email' => $email->id, 'token' => $email->tokens->first()->token . 'wrong_token']))
			->assertOk()
			->assertSeeText(__('user_email.token_not_found'));

		$this->assertFalse($email->fresh()->isConfirmed());
	}

	public function testIfAnotherConfirmedEmailExists()
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

	public function testIfOtherEmailExists()
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
}
