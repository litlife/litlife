<?php

namespace Tests\Feature\Forum\ForumGroup;

use App\ForumGroup;
use App\User;
use Tests\TestCase;

class ForumGroupCreatePolicyTest extends TestCase
{
	public function testIfUserHasPermission()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_group_handle = true;
		$admin->push();

		$this->assertTrue($admin->can('create', ForumGroup::class));
	}

	public function testIfUserDoesntHavePermission()
	{
		$user = factory(User::class)->create();
		$user->group->forum_group_handle = false;
		$user->push();

		$this->assertFalse($user->can('create', ForumGroup::class));
	}
}
