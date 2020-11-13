<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicOpenCloseTest extends TestCase
{
    public function testOpenClosePolicy()
    {
        $user = User::factory()->with_user_group()->create();
        $user->group->manipulate_topic = true;
        $user->push();

        $user2 = User::factory()->with_user_group()->create();

        $opened_topic = Topic::factory()->create();

        $closed_topic = Topic::factory()->closed()->create();

        $this->assertTrue($user->can('open', $closed_topic));
        $this->assertFalse($user2->can('open', $closed_topic));
        $this->assertFalse($user->can('open', $opened_topic));
        $this->assertFalse($user2->can('open', $opened_topic));

        $this->assertTrue($user->can('close', $opened_topic));
        $this->assertFalse($user2->can('close', $opened_topic));
        $this->assertFalse($user->can('close', $closed_topic));
        $this->assertFalse($user2->can('close', $closed_topic));
    }

    public function testOpen()
    {
        $topic = Topic::factory()->closed()->create();

        $user = User::factory()->administrator()->create();

        $this->actingAs($user)
            ->get(route('topics.open', $topic))
            ->assertRedirect();

        $topic->refresh();

        $this->assertFalse($topic->isClosed());
    }

    public function testClose()
    {
        $topic = Topic::factory()->create();

        $user = User::factory()->administrator()->create();

        $this->actingAs($user)
            ->get(route('topics.close', $topic))
            ->assertRedirect();

        $topic->refresh();

        $this->assertTrue($topic->isClosed());
    }
}
