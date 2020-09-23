<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumCreatePolicyTest extends TestCase
{
	public function testCantIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->add_forum_forum = false;
		$admin->push();

		$this->assertFalse($admin->can('create', Forum::class));
	}

	public function testCantIfDoesntHavePermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->add_forum_forum = true;
		$admin->push();

		$this->assertTrue($admin->can('create', Forum::class));
	}
}
