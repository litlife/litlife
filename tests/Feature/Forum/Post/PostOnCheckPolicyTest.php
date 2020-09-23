<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostOnCheckPolicyTest extends TestCase
{
	public function testCanIfHasPermission()
	{
		$user = factory(User::class)->create();
		$user->group->check_post_comments = true;
		$user->push();

		$this->assertTrue($user->can('viewOnCheck', Post::class));
	}

	public function testCantIfDoesntHavePermission()
	{
		$user = factory(User::class)->create();
		$user->group->check_post_comments = false;
		$user->push();

		$this->assertFalse($user->can('viewOnCheck', Post::class));
	}
}
