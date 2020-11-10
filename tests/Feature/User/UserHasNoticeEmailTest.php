<?php

namespace Tests\Feature\User;

use App\User;
use App\UserEmail;
use Tests\TestCase;

class UserHasNoticeEmailTest extends TestCase
{
	public function testTrueIfHaveNoticeEmailEmail()
	{
		$user = User::factory()->create();

		$email = UserEmail::factory()->create([
				'user_id' => $user->id,
				'notice' => true
			]);

		$this->assertTrue($user->hasNoticeEmail());
	}

	public function testFalseIfHaventNoticeEmailEmail()
	{
		$user = User::factory()->create();

		$email = UserEmail::factory()->create([
				'user_id' => $user->id,
				'notice' => false
			]);

		$this->assertFalse($user->hasNoticeEmail());
	}

	public function testFalseIfDoesntHaveAnyEmail()
	{
		$user = User::factory()->create();

		$this->assertFalse($user->hasNoticeEmail());
	}
}
