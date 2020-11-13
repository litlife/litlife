<?php

namespace Tests\Feature\Forum\Topic;

use App\Forum;
use App\Topic;
use App\User;
use Tests\TestCase;

class TopicCreatePolicyTest extends TestCase
{
    public function testTrue()
    {
        $user = User::factory()->create();
        $user->group->add_forum_topic = true;
        $user->push();

        $this->assertTrue($user->can('create', Topic::class));
    }

    public function testFalse()
    {
        $user = User::factory()->create();

        $topic = Topic::factory()->create();

        $this->assertFalse($user->can('create', Topic::class));
    }

    public function testCanIfForumPrivateAndUserInList()
    {
        $forum = Forum::factory()->private()->with_user_access()->create(['min_message_count' => 0]);

        $user = $forum->users_with_access->first();
        $user->group->add_forum_topic = true;
        $user->push();

        $this->assertTrue($user->can('create_topic', $forum));
    }

    public function testCantIfForumPrivateAndUserNotInList()
    {
        $forum = Forum::factory()->private()->with_user_access()->create(['min_message_count' => 0]);

        $user = User::factory()->create();
        $user->group->add_forum_topic = true;
        $user->push();

        $this->assertFalse($user->can('create_topic', $forum));
    }


    public function testCanCreateTopicIfHasPermissions()
    {
        $user = User::factory()->create();
        $user->group->add_forum_topic = true;
        $user->push();

        $forum = Forum::factory()->create(['min_message_count' => 0]);

        $this->assertTrue($user->can('create_topic', $forum));
    }

    public function testCantCreateTopicIfNoPermissions()
    {
        $user = User::factory()->create();
        $user->group->add_forum_topic = false;
        $user->push();

        $forum = Forum::factory()->create(['min_message_count' => 0]);

        $this->assertFalse($user->can('create_topic', $forum));
    }

    public function testCantCreateIfTopicIfIfThereAreNotEnoughMessages()
    {
        $user = User::factory()->create();
        $user->group->add_forum_topic = true;
        $user->forum_message_count = 30;
        $user->push();

        $forum = Forum::factory()->create(['min_message_count' => 40]);

        $this->assertFalse($user->can('create_topic', $forum));
    }
}
