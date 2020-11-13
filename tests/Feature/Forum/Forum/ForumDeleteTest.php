<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\Post;
use App\Topic;
use App\User;
use Tests\TestCase;

class ForumDeleteTest extends TestCase
{
    public function testDelete()
    {
        $admin = User::factory()->create();
        $admin->group->delete_forum_forum = true;
        $admin->push();

        $forum = Forum::factory()->create();

        $this->actingAs($admin)
            ->delete(route('forums.destroy', $forum))
            ->assertOk();

        $forum->refresh();

        $this->assertTrue($forum->trashed());
    }

    public function testRestore()
    {
        $admin = User::factory()->create();
        $admin->group->delete_forum_forum = true;
        $admin->push();

        $forum = Forum::factory()->create();

        $forum->delete();

        $this->actingAs($admin)
            ->delete(route('forums.destroy', $forum))
            ->assertOk();

        $forum->refresh();

        $this->assertFalse($forum->trashed());
    }

    public function testLastPostIfPostDeleted()
    {
        $forum = Forum::factory()->create();

        $topic = Topic::factory()->create(['forum_id' => $forum->id]);

        $topic2 = Topic::factory()->create(['forum_id' => $forum->id]);

        $post = Post::factory()->create(['topic_id' => $topic->id]);

        $post2 = Post::factory()->create(['topic_id' => $topic2->id, 'created_at' => $post->created_at->addSeconds(2)]);

        $forum->refresh();

        $this->assertEquals(2, $forum->post_count);
        $this->assertEquals(2, $forum->topic_count);
        $this->assertEquals($post2->id, $forum->last_post->id);
        $this->assertEquals($topic2->id, $forum->last_topic->id);

        $post2->delete();
        $forum->refresh();
        $topic2->refresh();

        $this->assertNull($topic2->last_post_created_at);

        $this->assertEquals(1, $forum->post_count);
        $this->assertEquals(2, $forum->topic_count);
        $this->assertNotNull($forum->last_post);
        $this->assertNotNull($forum->last_topic);
        $this->assertEquals($post->id, $forum->last_post->id);
        $this->assertEquals($topic->id, $forum->last_topic->id);
    }
}
