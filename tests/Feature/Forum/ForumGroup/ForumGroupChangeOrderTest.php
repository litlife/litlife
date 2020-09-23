<?php

namespace Tests\Feature\Forum\ForumGroup;

use App\ForumGroup;
use App\User;
use Tests\TestCase;

class ForumGroupChangeOrderTest extends TestCase
{
	public function testIfUserHasPermission()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_group_handle = true;
		$admin->push();

		$this->assertTrue($admin->can('change_order', ForumGroup::class));
	}

	public function testIfUserDoesntHavePermission()
	{
		$user = factory(User::class)->create();
		$user->group->forum_group_handle = false;
		$user->push();

		$this->assertFalse($user->can('change_order', ForumGroup::class));
	}

}
