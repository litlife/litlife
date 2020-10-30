<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\SupportQuestionMessage;
use App\User;
use Tests\TestCase;

class SupportQuestionSolvePolicyTest extends TestCase
{
	public function testCanIfUserHasPermissionAndStartedReview()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->states('review_starts')
			->create();

		$user = $supportQuestion->status_changed_user;
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertTrue($user->can('solve', $supportQuestion));
	}

	public function testCantIfDoesntHavePermission()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = false;
		$user->push();

		$this->assertFalse($user->can('solve', $supportQuestion));
	}

	public function testCantSolveIfAccepted()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->states('accepted')
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertFalse($user->can('solve', $supportQuestion));
	}

	public function testCantIfNotStartedReview()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->states('sent_for_review')
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertFalse($user->can('solve', $supportQuestion));
	}

	public function testCanIfUserThatCreatedRequestAndLastMessageIsNotCreatorOfRequest()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->states('sent_for_review')
			->create();

		$user = $supportQuestion->create_user;

		$message = factory(SupportQuestionMessage::class)
			->make();

		$supportQuestion->messages()->save($message);
		$supportQuestion->latest_message_id = $message->id;
		$supportQuestion->save();

		$this->assertTrue($user->can('solve', $supportQuestion));
	}

	public function testCantIfUserThatCreatedRequestAndLastMessageIsCreatorOfRequest()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->states('sent_for_review')
			->create();

		$user = $supportQuestion->create_user;

		$message = factory(SupportQuestionMessage::class)
			->make(['create_user_id' => $user->id]);

		$supportQuestion->messages()->save($message);
		$supportQuestion->latest_message_id = $message->id;
		$supportQuestion->save();

		$this->assertFalse($user->can('solve', $supportQuestion));
	}
}
