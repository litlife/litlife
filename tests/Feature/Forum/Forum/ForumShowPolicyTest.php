<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumShowPolicyTest extends TestCase
{
	public function testCanIfNotPrivate()
	{
		$user = User::factory()->create();

		$forum = Forum::factory()->create();

		$this->assertTrue($user->can('view', $forum));
	}

	public function testCanIfPrivateAndUserInAccessList()
	{
		$forum = Forum::factory()->private()->with_user_access()->create();

		$user = $forum->users_with_access()->first();

		$this->assertTrue($user->can('view', $forum));
	}

	public function testCantIfPrivateAndUserNotInAccessList()
	{
		$forum = Forum::factory()->private()->with_user_access()->create();

		$user = User::factory()->create();

		$this->assertFalse($user->can('view', $forum));
	}
}
