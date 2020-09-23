<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostMovePolicyTest extends TestCase
{
	public function testMovePolicy()
	{
		$user = factory(User::class)->create();

		$this->assertFalse($user->can('move', Post::class));

		$user->group->forum_move_post = true;
		$user->push();

		$this->assertTrue($user->can('move', Post::class));
	}
}
