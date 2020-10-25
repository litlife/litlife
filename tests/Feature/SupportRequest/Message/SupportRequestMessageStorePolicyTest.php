<?php

namespace Tests\Feature\SupportRequest\Message;

use App\SupportRequest;
use App\User;
use Tests\TestCase;

class SupportRequestMessageStorePolicyTest extends TestCase
{
	public function testCanIfUserCreatorOfRequest()
	{
		$supportRequest = factory(SupportRequest::class)
			->create();

		$user = $supportRequest->create_user;

		$this->assertTrue($user->can('createMessage', $supportRequest));
	}

	public function testCantIfUserNotCreatorOfRequest()
	{
		$supportRequest = factory(SupportRequest::class)
			->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('createMessage', $supportRequest));
	}

	public function testCanIfUserHasPermission()
	{
		$supportRequest = factory(SupportRequest::class)
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertTrue($user->can('createMessage', $supportRequest));
	}

	public function testCantIfUserDoesntHavePermission()
	{
		$supportRequest = factory(SupportRequest::class)
			->create();

		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = false;
		$user->push();

		$this->assertFalse($user->can('createMessage', $supportRequest));
	}
}
