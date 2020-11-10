<?php

namespace Tests\Feature\Complain;

use App\Complain;
use App\User;
use Tests\TestCase;

class ComplainUpdatePolicyTest extends TestCase
{
	public function testCanIfOnReview()
	{
		$complain = Complain::factory()->comment()->sent_for_review()->create();

		$user = $complain->create_user;
		$user->group->complain = true;
		$user->push();

		$this->assertTrue($user->can('update', $complain));
	}

	public function testCantIfOtherUser()
	{
		$complain = Complain::factory()->comment()->sent_for_review()->create();

		$user = User::factory()->create();
		$user->group->complain = true;
		$user->push();

		$this->assertFalse($user->can('update', $complain));
	}

	public function testCantIfReviewStarts()
	{
		$complain = Complain::factory()->comment()->review_starts()->create();

		$user = $complain->create_user;
		$user->group->complain = true;
		$user->push();

		$this->assertFalse($user->can('update', $complain));
	}

	public function testCantIfAccepted()
	{
		$complain = Complain::factory()->comment()->accepted()->create();

		$user = $complain->create_user;
		$user->group->complain = true;
		$user->push();

		$this->assertFalse($user->can('update', $complain));
	}
}
