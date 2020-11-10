<?php

namespace Tests\Feature;

use App\Enums\TopicLabelEnum;
use App\Post;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class IdeaTest extends TestCase
{
	public function testIndexIsOK()
	{
		$this->get(route('ideas.index'))
			->assertOk();
	}

	public function testStoreIsOK()
	{
		$user = User::factory()->admin()->create();

		$name = $this->faker->realText(100);
		$bb_text = $this->faker->realText(500);

		$response = $this->actingAs($user)
			->post(route('ideas.store'), [
				'name' => $name,
				'bb_text' => $bb_text,
				'enable_notifications_of_new_messages' => '0'
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$topic = $user->topics()->first();

		$response->assertRedirect(route('topics.show', $topic));

		$this->assertEquals($name, $topic->name);
		$this->assertTrue($topic->isUserCreator($user));

		$post = $topic->posts()->first();

		$this->assertEquals($bb_text, $post->bb_text);
		$this->assertTrue($post->isUserCreator($user));
		$this->assertTrue($post->isFixed());
		/*
				$subscription = $topic->user_subscriptions()
					->first();

				$this->assertEquals($user->id, $subscription->user_id);
				$this->assertEquals($topic->id, $subscription->topic_id);
				$this->assertNotNull($subscription->created_at);
				$this->assertNotNull($subscription->updated_at);
				*/
	}

	public function testCanCreateIfCanCreatePost()
	{
		$user = User::factory()->create();
		$user->group->add_forum_post = true;
		$user->push();

		$this->assertTrue($user->can('createAnIdea', User::class));
	}

	public function testCantCreateIfCantCreatePost()
	{
		$user = User::factory()->create();
		$user->group->add_forum_post = false;
		$user->push();

		$this->assertFalse($user->can('createAnIdea', User::class));
	}

	public function testIdeaWindowClose()
	{
		$user = User::factory()->create();

		$this->actingAs($user)
			->get(route('ideas.card.hide'))
			->assertOk()
			->assertJson(['result' => 'idea_card_is_hidden'])
			->assertSessionHas(['dont_show_idea_card' => true]);
	}

	public function testSearch()
	{
		$user = User::factory()->create();

		$name = $this->faker->realText(50);

		$this->actingAs($user)
			->get(route('ideas.search', ['name' => $name]))
			->assertOk();
	}

	public function testEnableNotifications()
	{
		$user = User::factory()->admin()->create();

		$name = $this->faker->realText(100);
		$bb_text = $this->faker->realText(500);

		$response = $this->actingAs($user)
			->post(route('ideas.store'), [
				'name' => $name,
				'bb_text' => $bb_text,
				'enable_notifications_of_new_messages' => '1'
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$topic = $user->topics()->first();

		$subscription = $topic->user_subscriptions()
			->first();

		$this->assertNotNull($subscription);
		$this->assertEquals($user->id, $subscription->user_id);
		$this->assertEquals($topic->id, $subscription->topic_id);
		$this->assertNotNull($subscription->created_at);
		$this->assertNotNull($subscription->updated_at);
	}

	public function testDisableNotifications()
	{
		$user = User::factory()->admin()->create();

		$name = $this->faker->realText(100);
		$bb_text = $this->faker->realText(500);

		$response = $this->actingAs($user)
			->post(route('ideas.store'), [
				'name' => $name,
				'bb_text' => $bb_text,
				'enable_notifications_of_new_messages' => 0
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$topic = $user->topics()->first();

		$subscription = $topic->user_subscriptions()
			->first();

		$this->assertNull($subscription);
	}

	public function testIdeaForumPostsState()
	{
		$post = Post::factory()->idea_forum_posts()->create();

		$topic = $post->topic;
		$forum = $topic->forum;

		$this->assertEquals(TopicLabelEnum::IdeaOnReview, $topic->label);
		$this->assertTrue($forum->isIdeaForum());
		$this->assertTrue($forum->isAutofixFirstPostInCreatedTopics());
		$this->assertTrue($forum->isOrderTopicsBasedOnFixPostLikes());
		$this->assertTrue($post->isFixed());
	}

	public function testCantCreateIdeaWithTheSameNameWithin5Minutes()
	{
		$user = User::factory()->admin()->create();

		$name = $this->faker->realText(50) . ' ' . Str::random(10);

		$this->actingAs($user)
			->post(route('ideas.store'),
				[
					'name' => $name,
					'bb_text' => $this->faker->realText(100),
					'enable_notifications_of_new_messages' => '0'
				]
			)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$this->actingAs($user)
			->post(route('ideas.store'),
				[
					'name' => $name,
					'bb_text' => $this->faker->realText(100),
					'enable_notifications_of_new_messages' => '0'
				]
			)
			->assertSessionHasErrors(['name' => __('idea.you_have_recently_created_a_idea_with_the_same_name')], null, 'idea')
			->assertRedirect();

		Carbon::setTestNow(now()->addMinutes(10));

		$this->actingAs($user)
			->post(route('ideas.store'),
				[
					'name' => $name,
					'bb_text' => $this->faker->realText(100),
					'enable_notifications_of_new_messages' => '0'
				]
			)
			->assertSessionHasNoErrors()
			->assertRedirect();
	}
}
