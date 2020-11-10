<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostComplainTest extends TestCase
{
	public function testCanComplain()
	{
		$user = User::factory()->create();
		$user->group->complain = true;
		$user->push();
		$user->refresh();

		$post = Post::factory()->create();

		$this->assertTrue($user->can('complain', $post));
	}
}
