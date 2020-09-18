<?php

namespace Tests\Feature\User;

use App\User;
use App\UserEmail;
use Tests\TestCase;

class UserHasNoticeEmailTest extends TestCase
{
	public function testTrueIfHaveNoticeEmailEmail()
	{
		$user = factory(User::class)
			->create();

		$email = factory(UserEmail::class)
			->create([
				'user_id' => $user->id,
				'notice' => true
			]);

		$this->assertTrue($user->hasNoticeEmail());
	}

	public function testFalseIfHaventNoticeEmailEmail()
	{
		$user = factory(User::class)
			->create();

		$email = factory(UserEmail::class)
			->create([
				'user_id' => $user->id,
				'notice' => false
			]);

		$this->assertFalse($user->hasNoticeEmail());
	}

	public function testFalseIfDoesntHaveAnyEmail()
	{
		$user = factory(User::class)
			->create();

		$this->assertFalse($user->hasNoticeEmail());
	}
}
