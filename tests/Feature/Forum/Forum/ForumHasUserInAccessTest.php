<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumHasUserInAccessTest extends TestCase
{
	public function testTrue()
	{
		$forum = factory(Forum::class)
			->states('private', 'with_user_access')
			->create();

		$user = $forum->users_with_access->first();

		$this->assertTrue($forum->hasUserInAccess($user));
	}

	public function testFalse()
	{
		$forum = factory(Forum::class)
			->states('private')
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($forum->hasUserInAccess($user));
	}
}
