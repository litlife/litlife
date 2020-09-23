<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumDeletePolicyTest extends TestCase
{
	public function testCanIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->delete_forum_forum = true;
		$admin->push();

		$forum = factory(Forum::class)->create();

		$this->assertTrue($admin->can('delete', $forum));

		$forum->delete();

		$this->assertTrue($admin->can('restore', $forum));
	}

	public function testCantIfDoesntHavePermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->delete_forum_forum = false;
		$admin->push();

		$forum = factory(Forum::class)->create();

		$this->assertFalse($admin->can('delete', $forum));

		$forum->delete();

		$this->assertFalse($admin->can('restore', $forum));
	}
}
