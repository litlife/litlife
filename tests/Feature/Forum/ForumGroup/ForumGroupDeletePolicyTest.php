<?php

namespace Tests\Feature\Forum\ForumGroup;

use App\ForumGroup;
use App\User;
use Tests\TestCase;

class ForumGroupDeletePolicyTest extends TestCase
{
	public function testIfUserHasPermission()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_group_handle = true;
		$admin->push();

		$forumGroup = factory(ForumGroup::class)
			->create();

		$this->assertTrue($admin->can('delete', $forumGroup));
	}

	public function testIfUserDoesntHavePermission()
	{
		$user = factory(User::class)->create();
		$user->group->forum_group_handle = false;
		$user->push();

		$forumGroup = factory(ForumGroup::class)
			->create();

		$this->assertFalse($user->can('delete', $forumGroup));
	}

}
