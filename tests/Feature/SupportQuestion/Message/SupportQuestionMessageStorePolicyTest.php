<?php

namespace Tests\Feature\SupportQuestion\Message;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionMessageStorePolicyTest extends TestCase
{
	public function testCanIfUserCreatorOfRequest()
	{
		$supportQuestion = SupportQuestion::factory()->create();

		$user = $supportQuestion->create_user;

		$this->assertTrue($user->can('createMessage', $supportQuestion));
	}

	public function testCantIfUserNotCreatorOfRequest()
	{
		$supportQuestion = SupportQuestion::factory()->create();

		$user = User::factory()->create();

		$this->assertFalse($user->can('createMessage', $supportQuestion));
	}

	public function testCantIfUserDoesntHavePermission()
	{
		$supportQuestion = SupportQuestion::factory()->create();

		$user = User::factory()->create();
		$user->group->reply_to_support_service = false;
		$user->push();

		$this->assertFalse($user->can('createMessage', $supportQuestion));
	}

	public function testCantIfUserNotStartReview()
	{
		$supportQuestion = SupportQuestion::factory()->sent_for_review()->create();

		$user = User::factory()->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertFalse($user->can('createMessage', $supportQuestion));
	}

	public function testCanIfUserStartReview()
	{
		$supportQuestion = SupportQuestion::factory()->review_starts()->create();

		$user = $supportQuestion->status_changed_user;
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertTrue($user->can('createMessage', $supportQuestion));
	}
}
