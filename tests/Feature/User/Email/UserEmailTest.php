<?php

namespace Tests\Feature\User\Email;

use App\Jobs\User\UpdateUserConfirmedMailboxCount;
use App\UserEmail;
use Tests\TestCase;

class UserEmailTest extends TestCase
{
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
}
