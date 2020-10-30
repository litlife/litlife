<?php

namespace Tests\Feature\SupportQuestion\Message;

use App\SupportQuestion;
use App\User;
use Tests\TestCase;

class SupportQuestionMessageStorePolicyTest extends TestCase
{
	public function testCanIfUserCreatorOfRequest()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->create();

		$user = $supportQuestion->create_user;

		$this->assertTrue($user->can('createMessage', $supportQuestion));
	}

	public function testCantIfUserNotCreatorOfRequest()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('createMessage', $supportQuestion));
	}

	public function testCantIfUserDoesntHavePermission()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = false;
		$user->push();

		$this->assertFalse($user->can('createMessage', $supportQuestion));
	}

	public function testCantIfUserNotStartReview()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->states('sent_for_review')
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertFalse($user->can('createMessage', $supportQuestion));
	}

	public function testCanIfUserStartReview()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->states('review_starts')
			->create();

		$user = $supportQuestion->status_changed_user;
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertTrue($user->can('createMessage', $supportQuestion));
	}
}
