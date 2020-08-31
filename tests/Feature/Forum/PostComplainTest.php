<?php

namespace Tests\Feature\Forum;

use App\Post;
use App\User;
use Tests\TestCase;

class PostComplainTest extends TestCase
{
	public function testCanComplain()
	{
		$user = factory(User::class)->create();
		$user->group->complain = true;
		$user->push();
		$user->refresh();

		$post = factory(Post::class)
			->create();

		$this->assertTrue($user->can('complain', $post));
	}
}
