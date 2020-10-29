<?php

namespace Tests\Feature\SupportRequest;

use App\SupportRequest;
use App\SupportRequestMessage;
use App\User;
use Tests\TestCase;

class SupportRequestSolvePolicyTest extends TestCase
{
	public function testCanIfUserHasPermissionAndStartedReview()
	{
		$supportRequest = factory(SupportRequest::class)
			->states('review_starts')
			->create();

		$user = $supportRequest->status_changed_user;
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertTrue($user->can('solve', $supportRequest));
	}

	public function testCantIfDoesntHavePermission()
	{
		$supportRequest = factory(SupportRequest::class)
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = false;
		$user->push();

		$this->assertFalse($user->can('solve', $supportRequest));
	}

	public function testCantSolveIfAccepted()
	{
		$supportRequest = factory(SupportRequest::class)
			->states('accepted')
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertFalse($user->can('solve', $supportRequest));
	}

	public function testCantIfNotStartedReview()
	{
		$supportRequest = factory(SupportRequest::class)
			->states('sent_for_review')
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertFalse($user->can('solve', $supportRequest));
	}

	public function testCanIfUserThatCreatedRequestAndLastMessageIsNotCreatorOfRequest()
	{
		$supportRequest = factory(SupportRequest::class)
			->states('sent_for_review')
			->create();

		$user = $supportRequest->create_user;

		$message = factory(SupportRequestMessage::class)
			->make();

		$supportRequest->messages()->save($message);
		$supportRequest->latest_message_id = $message->id;
		$supportRequest->save();

		$this->assertTrue($user->can('solve', $supportRequest));
	}

	public function testCantIfUserThatCreatedRequestAndLastMessageIsCreatorOfRequest()
	{
		$supportRequest = factory(SupportRequest::class)
			->states('sent_for_review')
			->create();

		$user = $supportRequest->create_user;

		$message = factory(SupportRequestMessage::class)
			->make(['create_user_id' => $user->id]);

		$supportRequest->messages()->save($message);
		$supportRequest->latest_message_id = $message->id;
		$supportRequest->save();

		$this->assertFalse($user->can('solve', $supportRequest));
	}
}
