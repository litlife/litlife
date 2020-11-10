<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumChangeOrderTest extends TestCase
{
	public function testCantChangeOrderForumIfHasPermissions()
	{
		$admin = User::factory()->create();
		$admin->group->forum_list_manipulate = false;
		$admin->push();

		$forum = Forum::factory()->create();

		$this->assertFalse($admin->can('change_order', $forum));
	}

	public function testCanChangeOrderForumIfHasPermissions()
	{
		$admin = User::factory()->create();
		$admin->group->forum_list_manipulate = true;
		$admin->push();

		$forum = Forum::factory()->create();

		$this->assertTrue($admin->can('change_order', $forum));
	}
}
