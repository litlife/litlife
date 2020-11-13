<?php

namespace Tests\Feature\Forum\Topic;

use App\Forum;
use App\Post;
use App\User;
use Tests\TestCase;

class TopicMoveTest extends TestCase
{
    public function testMove()
    {
        $user = User::factory()->create();
        $user->group->manipulate_topic = true;
        $user->push();

        $post = Post::factory()->create()->fresh();
        $post2 = Post::factory()->create(['topic_id' => $post->topic->id])->fresh();

        $forum = Forum::factory()->create()->fresh();

        $topic = $post->topic;
        $forum2 = $post->topic->forum;

        $response = $this->actingAs($user)
            ->post(route('topics.move', ['topic' => $topic->id]),
                ['forum' => $forum->id]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $topic->refresh();
        $forum->refresh();
        $forum2->refresh();

        $this->assertEquals($post2->id, $topic->last_post_id);
        $this->assertEquals($post2->created_at, $topic->last_post_created_at);

        $this->assertEquals($post2->id, $forum->last_post_id);
        $this->assertEquals($topic->id, $forum->last_topic_id);

        $this->assertNull($forum2->last_post_id);
        $this->assertNull($forum2->last_topic_id);
    }
}
