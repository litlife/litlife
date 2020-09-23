<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumShowPolicyTest extends TestCase
{
	public function testCanIfNotPrivate()
	{
		$user = factory(User::class)->create();

		$forum = factory(Forum::class)->create();

		$this->assertTrue($user->can('view', $forum));
	}

	public function testCanIfPrivateAndUserInAccessList()
	{
		$forum = factory(Forum::class)
			->states('private', 'with_user_access')
			->create();

		$user = $forum->users_with_access()->first();

		$this->assertTrue($user->can('view', $forum));
	}

	public function testCantIfPrivateAndUserNotInAccessList()
	{
		$forum = factory(Forum::class)
			->states('private', 'with_user_access')
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('view', $forum));
	}
}
