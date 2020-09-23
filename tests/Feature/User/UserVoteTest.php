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
	public function testRouteIsOk()
	{
		$vote = factory(BookVote::class)
			->create();

		$this->actingAs($vote->create_user)
			->get(route('users.votes', ['user' => $vote->create_user]))
			->assertOk();
	}

	public function testRouteIsOkViewOtherUser()
	{
		$vote = factory(BookVote::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.votes', ['user' => $vote->create_user]))
			->assertOk();
	}
}
