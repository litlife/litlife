<?php

namespace Tests\Feature\Forum\Post;

use App\Topic;
use App\User;
use Tests\TestCase;

class PostFixTest extends TestCase
{
	public function testFix()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$topic = factory(Topic::class)
			->states('with_post')
			->create();

		$post = $topic->posts()->first();

		$this->assertFalse($post->isFixed());

		$this->actingAs($user)
			->get(route('posts.fix', $post))
			->assertRedirect();

		$post->refresh();

		$this->assertTrue($post->isFixed());
	}

	public function testUnFix()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$topic = factory(Topic::class)
			->states('with_fixed_post')
			->create();

		$post = $topic->posts()->first();

		$this->assertTrue($post->isFixed());

		$this->actingAs($user)
			->get(route('posts.unfix', $post))
			->assertRedirect();

		$post->refresh();

		$this->assertFalse($post->isFixed());
	}
}
