<?php

namespace Tests\Feature\SupportRequest;

use App\SupportRequest;
use App\User;
use Tests\TestCase;

class SupportRequestIndexPolicyTest extends TestCase
{
	public function testCanIfHasPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertTrue($user->can('index', SupportRequest::class));
	}

	public function testCantIfDoesntHavePermissions()
	{
		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = false;
		$user->push();

		$this->assertFalse($user->can('index', SupportRequest::class));
	}
}
