<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicEditPolicyTest extends TestCase
{
    public function testUpdatePolicy()
    {
        $user = User::factory()->with_user_group()->create();
        $user->group->edit_forum_self_topic = true;
        $user->push();

        $user2 = User::factory()->with_user_group()->create();

        $topic = Topic::factory()->create();
        $topic->create_user_id = $user->id;
        $topic->push();

        $this->assertTrue($user->can('update', $topic));
        $this->assertFalse($user2->can('update', $topic));

        //

        $user = User::factory()->create();
        $user->group->edit_forum_self_topic = true;
        $user->group->edit_forum_other_user_topic = true;
        $user->push();

        $user2 = User::factory()->with_user_group()->create();

        $topic = Topic::factory()->create();
        $topic->create_user_id = $user->id;
        $topic->push();

        $topic2 = Topic::factory()->create();
        $topic2->create_user_id = $user2->id;
        $topic2->push();

        $this->assertTrue($user->can('update', $topic));
        $this->assertTrue($user->can('update', $topic2));
        $this->assertFalse($user2->can('update', $topic));
        $this->assertFalse($user2->can('update', $topic2));
    }
}
