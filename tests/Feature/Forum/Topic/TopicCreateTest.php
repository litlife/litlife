<?php

namespace Tests\Feature\Forum\Topic;

use App\Enums\TopicLabelEnum;
use App\Forum;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class TopicCreateTest extends TestCase
{
    public function testStoreHttp()
    {
        $user = User::factory()->create();
        $user->group->add_forum_topic = true;
        $user->push();

        $forum = Forum::factory()->create()->fresh();

        $response = $this->actingAs($user)
            ->post(route('topics.store', ['forum' => $forum->id]),
                [
                    'name' => $this->faker->realText(50).' '.Str::random(10),
                    'description' => $this->faker->realText(100),
                    'bb_text' => $this->faker->realText(100)
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $topic = $forum->topics()->first();

        $response->assertRedirect(route('topics.show', ['topic' => $topic->id]));

        $post = $topic->posts()->first();

        $this->assertEquals(1, $topic->posts()->count());
        $this->assertEquals($post->id, $topic->last_post_id);
        $this->assertEquals($post->created_at, $topic->last_post_created_at);

        $forum->refresh();

        $this->assertEquals(1, $forum->topic_count);
        $this->assertEquals($post->id, $forum->last_post_id);
        $this->assertEquals($topic->id, $forum->last_topic_id);

        $user->refresh();

        $this->assertEquals(1, $user->topics_count);
    }

    public function testNotAttachLabelIdeaOnReviewIfNotIdeaForum()
    {
        $admin = User::factory()->create();
        $admin->group->add_forum_topic = true;
        $admin->push();

        $forum = Forum::factory()->create();

        $this->assertFalse($forum->isIdeaForum());

        $response = $this->actingAs($admin)
            ->post(route('topics.store', ['forum' => $forum->id]),
                [
                    'name' => $this->faker->realText(50),
                    'description' => $this->faker->realText(200),
                    'bb_text' => $this->faker->realText(100)
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $topic = $forum->topics()->first();

        $this->assertNull($topic->label);
    }

    public function testAttachLabelIdeaOnReviewIfIdeaForum()
    {
        $admin = User::factory()->create();
        $admin->group->add_forum_topic = true;
        $admin->push();

        $forum = Forum::factory()->create();
        $forum->is_idea_forum = true;
        $forum->save();

        $this->assertTrue($forum->fresh()->isIdeaForum());

        $response = $this->actingAs($admin)
            ->post(route('topics.store', ['forum' => $forum->id]),
                [
                    'name' => $this->faker->realText(50),
                    'description' => $this->faker->realText(100),
                    'bb_text' => $this->faker->realText(100)
                ]
            )
            ->assertSessionHasNoErrors();

        $topic = $forum->topics()->first();

        $this->assertEquals(TopicLabelEnum::IdeaOnReview, $topic->label);
    }

    public function testCantCreateTopicWithTheSameNameWithin5Minutes()
    {
        $user = User::factory()->admin()->create();

        $forum = Forum::factory()->create();

        $name = $this->faker->realText(50).' '.Str::random(10);

        $this->actingAs($user)
            ->post(route('topics.store', ['forum' => $forum->id]),
                [
                    'name' => $name,
                    'description' => $this->faker->realText(100),
                    'bb_text' => $this->faker->realText(100)
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertEquals(1, $forum->topics()->count());

        $this->actingAs($user)
            ->post(route('topics.store', ['forum' => $forum->id]),
                [
                    'name' => $name,
                    'description' => $this->faker->realText(100),
                    'bb_text' => $this->faker->realText(100)
                ]
            )
            ->assertSessionHasErrors(['name' => __('topic.you_have_recently_created_a_theme_with_the_same_name')])
            ->assertRedirect();

        Carbon::setTestNow(now()->addMinutes(10));

        $this->actingAs($user)
            ->post(route('topics.store', ['forum' => $forum->id]),
                [
                    'name' => $name,
                    'description' => $this->faker->realText(100),
                    'bb_text' => $this->faker->realText(100)
                ]
            )
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertEquals(2, $forum->topics()->count());
    }

    public function testAutofixFirstPostInCreatedTopicsEnabled()
    {
        $admin = User::factory()->create();
        $admin->group->add_forum_topic = true;
        $admin->push();

        $forum = Forum::factory()->create(['autofix_first_post_in_created_topics' => true]);

        $this->assertTrue($forum->isAutofixFirstPostInCreatedTopics());

        $response = $this->actingAs($admin)
            ->post(route('topics.store', ['forum' => $forum->id]),
                [
                    'name' => $this->faker->realText(100),
                    'description' => $this->faker->realText(100),
                    'bb_text' => $this->faker->realText(100),
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertTrue($admin->posts()->first()->isFixed());
    }

    public function testAutofixFirstPostInCreatedTopicsDisabled()
    {
        $admin = User::factory()->create();
        $admin->group->add_forum_topic = true;
        $admin->push();

        $forum = Forum::factory()->create(['autofix_first_post_in_created_topics' => false]);

        $this->assertFalse($forum->isAutofixFirstPostInCreatedTopics());

        $response = $this->actingAs($admin)
            ->post(route('topics.store', ['forum' => $forum->id]),
                [
                    'name' => $this->faker->realText(100),
                    'description' => $this->faker->realText(100),
                    'bb_text' => $this->faker->realText(100),
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertFalse($admin->posts()->first()->isFixed());
    }
}
