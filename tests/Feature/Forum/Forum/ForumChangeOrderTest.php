<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumChangeOrderTest extends TestCase
{
	public function testCantChangeOrderForumIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_list_manipulate = false;
		$admin->push();

		$forum = factory(Forum::class)->create();

		$this->assertFalse($admin->can('change_order', $forum));
	}

	public function testCanChangeOrderForumIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_list_manipulate = true;
		$admin->push();

		$forum = factory(Forum::class)->create();

		$this->assertTrue($admin->can('change_order', $forum));
	}
}
