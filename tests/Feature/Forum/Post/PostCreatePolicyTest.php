<?php

namespace Tests\Feature\Forum\Post;

use App\Forum;
use App\Post;
use App\Topic;
use App\User;
use Tests\TestCase;

class PostCreatePolicyTest extends TestCase
{
	public function testCanIfHasPermission()
	{
		$user = factory(User::class)->create();
		$user->group->add_forum_post = true;
		$user->push();

		$topic = factory(Topic::class)
			->create();

		$this->assertTrue($user->can('create_post', $topic));
	}

	public function testCantIfDoesntHavePermission()
	{
		$user = factory(User::class)->create();
		$user->group->add_forum_post = false;
		$user->push();

		$topic = factory(Topic::class)
			->create();

		$this->assertFalse($user->can('create_post', $topic));
	}

	public function testCantIfTopicDeleted()
	{
		$post = factory(Post::class)
			->create();

		$topic = $post->topic;

		$topic->delete();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('create_post', $topic));
	}

	public function testCantReplyIfOnReview()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$post = factory(Post::class)
			->states('sent_for_review')
			->create();

		$this->assertFalse($user->can('reply', $post));
	}

	public function testCanReplyToOtherPost()
	{
		$user = factory(User::class)->create();

		$post = factory(Post::class)->create();

		$this->assertTrue($user->can('reply', $post));
	}

	public function testCantReplyToSelfPost()
	{
		$user = factory(User::class)->create();

		$post = factory(Post::class)->create();
		$post->create_user()->associate($user);
		$post->push();

		$this->assertFalse($user->can('reply', $post));
	}

	public function testCanIfForumPrivateAndUserInList()
	{
		$forum = factory(Forum::class)
			->states('private', 'with_user_access', 'with_topic')
			->create();

		$topic = $forum->topics()->first();

		$user = $forum->users_with_access->first();
		$user->group->add_forum_post = true;
		$user->push();

		$this->assertTrue($user->can('create_post', $topic));
	}

	public function testCantIfForumPrivateAndUserNotInList()
	{
		$forum = factory(Forum::class)
			->states('private', 'with_topic')
			->create();

		$topic = $forum->topics()->first();

		$user = factory(User::class)->create();
		$user->group->add_forum_post = true;
		$user->push();

		$this->assertFalse($user->can('create_post', $topic));
	}

	public function testCreatePolicy()
	{
		// create_post

		$admin = factory(User::class)->create();
		$admin->group->add_forum_post = true;
		$admin->push();

		$user = factory(User::class)->create();
		$user->push();

		$topic = factory(Topic::class)->create();
		$topic->closed = false;
		$topic->push();

		$this->assertTrue($admin->can('create_post', $topic));
		$this->assertTrue($user->can('create_post', $topic));

		//

		$admin = factory(User::class)->create();
		$admin->group->add_forum_post = true;
		$admin->push();

		$user = factory(User::class)->create();
		$user->group->add_forum_post = true;
		$user->push();

		$topic = factory(Topic::class)->create();
		$topic->closed = true;
		$topic->push();

		$this->assertFalse($admin->can('create_post', $topic));
		$this->assertFalse($user->can('create_post', $topic));

		// create

		$admin = factory(User::class)->create();
		$admin->group->add_forum_topic = true;
		$admin->push();

		$user = factory(User::class)
			->states('with_user_group')
			->create();

		$topic = factory(Topic::class)->create();

		$this->assertTrue($admin->can('create', $topic));
		$this->assertFalse($user->can('create', $topic));
	}
}
