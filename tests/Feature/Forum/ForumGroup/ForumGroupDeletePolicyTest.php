<?php

namespace Tests\Feature\Forum\ForumGroup;

use App\ForumGroup;
use App\User;
use Tests\TestCase;

class ForumGroupDeletePolicyTest extends TestCase
{
	public function testIfUserHasPermission()
	{
		$admin = User::factory()->create();
		$admin->group->forum_group_handle = true;
		$admin->push();

		$forumGroup = ForumGroup::factory()->create();

		$this->assertTrue($admin->can('delete', $forumGroup));
	}

	public function testIfUserDoesntHavePermission()
	{
		$user = User::factory()->create();
		$user->group->forum_group_handle = false;
		$user->push();

		$forumGroup = ForumGroup::factory()->create();

		$this->assertFalse($user->can('delete', $forumGroup));
	}

}
