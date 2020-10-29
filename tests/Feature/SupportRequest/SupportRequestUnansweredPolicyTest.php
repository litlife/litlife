<?php

namespace Tests\Feature\SupportRequest;

use App\SupportRequest;
use App\User;
use Tests\TestCase;

class SupportRequestUnsolvedPolicyTest extends TestCase
{
	public function testCanIfHasPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->assertTrue($user->can('view_unsolved', SupportRequest::class));
	}

	public function testCantIfDoesntHavePermissions()
	{
		$user = factory(User::class)->create();
		$user->group->reply_to_support_service = false;
		$user->push();

		$this->assertFalse($user->can('view_unsolved', SupportRequest::class));
	}
}
