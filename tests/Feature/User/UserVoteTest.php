<?php

namespace Tests\Feature\User;

use App\BookVote;
use App\User;
use Tests\TestCase;

class UserVoteTest extends TestCase
{


	/**
	 * A basic test example.
	 *
	 * @return void
	 */

	public function testShowHttp()
	{
		$vote = factory(BookVote::class)
			->create()
			->fresh();

		$this->actingAs($vote->create_user)
			->get(route('users.votes', ['user' => $vote->create_user]))
			->assertOk();

		$user = factory(User::class)
			->create()
			->fresh();

		$this->actingAs($user)
			->get(route('users.votes', ['user' => $vote->create_user]))
			->assertOk();
	}
}
