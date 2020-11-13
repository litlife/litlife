<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use App\User;
use Tests\TestCase;

class TopicArchiveTest extends TestCase
{
    public function testIndexHttp()
    {
        Topic::archived()->delete();

        $user = User::factory()->create();

        $topic = Topic::factory()->archived()->create();

        $this->actingAs($user)
            ->get(route('topics.archived'))
            ->assertOk()
            ->assertSeeText(__('topic.archived_topics'))
            ->assertSeeText($topic->name);
    }

    public function testArchive()
    {
        $user = User::factory()->create();
        $user->group->manipulate_topic = true;
        $user->push();

        $topic = Topic::factory()->create();

        $this->actingAs($user)
            ->get(route('topics.archive', ['topic' => $topic]))
            ->assertRedirect();

        $topic->refresh();

        $this->assertTrue($topic->isArchived());
    }

    public function testUnarchive()
    {
        $user = User::factory()->create();
        $user->group->manipulate_topic = true;
        $user->push();

        $topic = Topic::factory()->archived()->create();

        $this->actingAs($user)
            ->get(route('topics.unarchive', ['topic' => $topic]))
            ->assertRedirect();

        $topic->refresh();

        $this->assertFalse($topic->isArchived());
    }
}
