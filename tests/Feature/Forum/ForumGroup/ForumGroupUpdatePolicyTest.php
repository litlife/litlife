<?php

namespace Tests\Feature\Forum\ForumGroup;

use App\ForumGroup;
use App\User;
use Tests\TestCase;

class ForumGroupUpdatePolicyTest extends TestCase
{
	public function tesCantIfUserHasPermission()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_group_handle = true;
		$admin->push();

		$forumGroup = factory(ForumGroup::class)
			->create();

		$this->assertTrue($admin->can('update', $forumGroup));
	}

	public function testCantIfUserDoesntHavePermission()
	{
		$user = factory(User::class)->create();
		$user->group->forum_group_handle = false;
		$user->push();

		$forumGroup = factory(ForumGroup::class)
			->create();

		$this->assertFalse($user->can('update', $forumGroup));
	}

}
