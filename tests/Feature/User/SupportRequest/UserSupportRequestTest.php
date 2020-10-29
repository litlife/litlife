<?php

namespace Tests\Feature\User\SupportRequest;

use App\User;
use Tests\TestCase;

class UserSupportRequestTest extends TestCase
{
	public function testCanIfUser()
	{
		$user = factory(User::class)->create();

		$this->assertTrue($user->can('view_list_support_requests', $user));
	}

	public function testCanIfOtherUser()
	{
		$user = factory(User::class)->create();

		$user2 = factory(User::class)->create();

		$this->assertFalse($user->can('view_list_support_requests', $user2));
	}
}
