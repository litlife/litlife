<?php

namespace Tests\Feature\Forum\Topic;

use App\Post;
use App\Topic;
use App\User;
use Tests\TestCase;

class TopicRestoreTest extends TestCase
{
    public function testRestore()
    {
        $user = User::factory()->admin()->create();

        $post = Post::factory()->create();

        $topic = $post->topic;

        $topic->delete();

        $this->assertTrue($topic->trashed());
        $this->assertEquals(0, $topic->create_user->topics_count);
        $this->assertEquals(0, $topic->forum->topic_count);

        $response = $this->actingAs($user)
            ->delete(route('topics.destroy', $topic))
            ->assertOk();

        $topic->refresh();
        $post->refresh();

        $this->assertFalse($topic->trashed());
        $this->assertFalse($post->trashed());
        $this->assertEquals(1, $topic->create_user->topics_count);
        $this->assertEquals(1, $topic->forum->topic_count);
    }

    public function testRestoreOnlyPostsThatAreOlderThanDeletingTheTopic()
    {
        $topic = Topic::factory()
            ->create(['deleted_at' => now()]);

        $post = Post::factory()
            ->make(['deleted_at' => $topic->deleted_at->subMinute()]);

        $post2 = Post::factory()
            ->make(['deleted_at' => $topic->deleted_at->addMinute()]);

        $topic->posts()->save($post);
        $topic->posts()->save($post2);

        $topic->restore();

        $post->refresh();
        $post2->refresh();
        $topic->refresh();

        $this->assertFalse($topic->trashed());
        $this->assertTrue($post->trashed());
        $this->assertFalse($post2->trashed());
    }
}
