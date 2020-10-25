<?php

namespace Tests\Feature\SupportRequest;

use App\SupportRequest;
use App\User;
use Tests\TestCase;

class SupportRequestCreatePolicyTest extends TestCase
{
	public function testCanIfHasPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->send_a_support_request = true;
		$user->push();

		$this->assertTrue($user->can('create', SupportRequest::class));
	}

	public function testCantIfDoesntHavePermissions()
	{
		$user = factory(User::class)->create();
		$user->group->send_a_support_request = false;
		$user->push();

		$this->assertFalse($user->can('create', SupportRequest::class));
	}
}
