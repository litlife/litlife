<?php

namespace Tests\Feature\User\Email;

use App\User;
use App\UserEmail;
use Tests\TestCase;

class UserEmailUnrescueTest extends TestCase
{
	public function test()
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

	public function testErrorAtLeastOneEmailForRescue()
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

	public function testErrorNotConfirmed()
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

	public function testEnable()
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
}
