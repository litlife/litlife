<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumHasUserInAccessTest extends TestCase
{
	public function testTrue()
	{
		$forum = Forum::factory()->private()->with_user_access()->create();

		$user = $forum->users_with_access->first();

		$this->assertTrue($forum->hasUserInAccess($user));
	}

	public function testFalse()
	{
		$forum = Forum::factory()->private()->create();

		$user = User::factory()->create();

		$this->assertFalse($forum->hasUserInAccess($user));
	}
}
