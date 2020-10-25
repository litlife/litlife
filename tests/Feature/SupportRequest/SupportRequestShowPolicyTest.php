<?php

namespace Tests\Feature\SupportRequest;

use App\SupportRequest;
use App\User;
use Tests\TestCase;

class SupportRequestShowPolicyTest extends TestCase
{
	public function testCanIfUserCreator()
	{
		$supportRequest = factory(SupportRequest::class)
			->create();

		$user = $supportRequest->create_user;

		$this->assertTrue($user->can('show', $supportRequest));
	}

	public function testCanIfHasPermissionToReply()
	{
		$supportRequest = factory(SupportRequest::class)
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertTrue($user->can('show', $supportRequest));
	}

	public function testCantIfUserNotCreator()
	{
		$supportRequest = factory(SupportRequest::class)
			->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('show', $supportRequest));
	}

	public function testCantIfHasDoesntHavePermissionToReply()
	{
		$supportRequest = factory(SupportRequest::class)
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = false;
		$user->push();

		$this->assertFalse($user->can('show', $supportRequest));
	}
}
