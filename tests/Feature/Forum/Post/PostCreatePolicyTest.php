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
        $user = User::factory()->create();
        $user->group->add_forum_post = true;
        $user->push();

        $topic = Topic::factory()->create();

        $this->assertTrue($user->can('create_post', $topic));
    }

    public function testCantIfDoesntHavePermission()
    {
        $user = User::factory()->create();
        $user->group->add_forum_post = false;
        $user->push();

        $topic = Topic::factory()->create();

        $this->assertFalse($user->can('create_post', $topic));
    }

    public function testCantIfTopicDeleted()
    {
        $post = Post::factory()->create();

        $topic = $post->topic;

        $topic->delete();

        $user = User::factory()->create();

        $this->assertFalse($user->can('create_post', $topic));
    }

    public function testCantReplyIfOnReview()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->sent_for_review()->create();

        $this->assertFalse($user->can('reply', $post));
    }

    public function testCanReplyToOtherPost()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create();

        $this->assertTrue($user->can('reply', $post));
    }

    public function testCantReplyToSelfPost()
    {
        $user = User::factory()->create();

        $post = Post::factory()->create();
        $post->create_user()->associate($user);
        $post->push();

        $this->assertFalse($user->can('reply', $post));
    }

    public function testCanIfForumPrivateAndUserInList()
    {
        $forum = Forum::factory()->private()->with_user_access()->with_topic()->create();

        $topic = $forum->topics()->first();

        $user = $forum->users_with_access->first();
        $user->group->add_forum_post = true;
        $user->push();

        $this->assertTrue($user->can('create_post', $topic));
    }

    public function testCantIfForumPrivateAndUserNotInList()
    {
        $forum = Forum::factory()->private()->with_topic()->create();

        $topic = $forum->topics()->first();

        $user = User::factory()->create();
        $user->group->add_forum_post = true;
        $user->push();

        $this->assertFalse($user->can('create_post', $topic));
    }

    public function testCreatePolicy()
    {
        // create_post

        $admin = User::factory()->create();
        $admin->group->add_forum_post = true;
        $admin->push();

        $user = User::factory()->create();
        $user->push();

        $topic = Topic::factory()->create();
        $topic->closed = false;
        $topic->push();

        $this->assertTrue($admin->can('create_post', $topic));
        $this->assertTrue($user->can('create_post', $topic));

        //

        $admin = User::factory()->create();
        $admin->group->add_forum_post = true;
        $admin->push();

        $user = User::factory()->create();
        $user->group->add_forum_post = true;
        $user->push();

        $topic = Topic::factory()->create();
        $topic->closed = true;
        $topic->push();

        $this->assertFalse($admin->can('create_post', $topic));
        $this->assertFalse($user->can('create_post', $topic));

        // create

        $admin = User::factory()->create();
        $admin->group->add_forum_topic = true;
        $admin->push();

        $user = User::factory()->with_user_group()->create();

        $topic = Topic::factory()->create();

        $this->assertTrue($admin->can('create', $topic));
        $this->assertFalse($user->can('create', $topic));
    }
}
