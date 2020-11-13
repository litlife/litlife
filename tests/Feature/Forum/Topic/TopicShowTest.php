<?php

namespace Tests\Feature\Forum\Topic;

use App\Post;
use App\Topic;
use App\User;
use Tests\TestCase;

class TopicShowTest extends TestCase
{
    public function testShowHttp()
    {
        $topic = Topic::factory()->create();

        $this->get(route('topics.show', $topic))
            ->assertOk()
            ->assertSeeText(__('post.nothing_found'));
    }

    public function testShowHttpDontSeeNothingFoundIfFixPost()
    {
        $post = Post::factory()->create();

        $post->fix();

        $this->get(route('topics.show', $post->topic))
            ->assertOk()
            ->assertDontSeeText(__('post.nothing_found'));
    }

    public function testViewCount()
    {
        $post = Post::factory()->create();

        $topic = $post->topic;

        $this->get(route('topics.show', $topic))
            ->assertOk();
        $topic->refresh();
        $this->assertEquals(1, $topic->view_count);

        $this->get(route('topics.show', $topic))
            ->assertOk();
        $topic->refresh();
        $this->assertEquals(2, $topic->view_count);

        $this->get(route('topics.show', $topic))
            ->assertOk();
        $topic->refresh();
        $this->assertEquals(3, $topic->view_count);
    }

    public function testViewPrivateTopic()
    {
        $post = Post::factory()->create();

        $user = $post->create_user;

        $forum = $post->forum;
        $forum->private = true;
        $forum->save();

        $this->assertFalse($user->can('view', $forum));

        $forum->users_with_access()->sync([$user->id]);
        $forum->refresh();

        $this->assertTrue($user->can('view', $forum));

        $response = $this->actingAs($user)
            ->get(route('topics.show', ['topic' => $post->topic->id]))
            ->assertOk()
            ->assertSeeText($forum->name);

        $other_user = User::factory()->create();

        $response = $this->actingAs($other_user)
            ->get(route('topics.show', ['topic' => $post->topic->id]))
            ->assertForbidden();

        $response = $this->get(route('topics.show', ['topic' => $post->topic->id]))
            ->assertForbidden();
    }

    public function testForumDeletedShowTopic()
    {
        $topic = Topic::factory()->create();

        $topic->forum->delete();

        $this->get(route('topics.show', $topic))
            ->assertNotFound();

        $topic->forum->forceDelete();

        $this->get(route('topics.show', $topic))
            ->assertNotFound();
    }

    public function testIsNotFoundIfTopicDeleted()
    {
        $topic = Topic::factory()->create();

        $topic->delete();

        $this->get(route('topics.show', $topic))
            ->assertNotFound();
    }

    public function testCanSeeArchivedTopicPosts()
    {
        $topic = Topic::factory()->archived()->with_post()->create();

        $post = $topic->posts()->first();

        $this->get(route('topics.show', $topic))
            ->assertOk()
            ->assertSeeText($post->html_text);
    }

    public function testViewInTopicIfOnReview()
    {
        $post = Post::factory()->create();
        $post->statusSentForReview();
        $post->save();

        $user = User::factory()->create();

        $this->actingAs($post->create_user)
            ->get(route('topics.show', $post->topic))
            ->assertSeeText($post->text);

        $this->actingAs($user)
            ->get(route('topics.show', $post->topic))
            ->assertDontSeeText($post->text)
            ->assertSeeText(trans_choice('post.on_check', 1));
    }

    public function testPerPage()
    {
        $topic = Topic::factory()->create();

        $response = $this->get(route('topics.show', ['topic' => $topic, 'per_page' => 5]))
            ->assertOk();

        $this->assertEquals(10, $response->original->gatherData()['items']->perPage());

        $response = $this->get(route('topics.show', ['topic' => $topic, 'per_page' => 200]))
            ->assertOk();

        $this->assertEquals(100, $response->original->gatherData()['items']->perPage());
    }
}
