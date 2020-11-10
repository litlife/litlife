<?php

namespace Tests\Feature\Forum\Topic;

use App\Enums\TopicLabelEnum;
use App\Forum;
use App\Topic;
use App\User;
use Tests\TestCase;

class TopicEditTest extends TestCase
{
	public function testEditNotFound()
	{
		$admin = User::factory()->create();
		$admin->push();

		$topic = Topic::factory()->create();

		$id = $topic->id;

		$topic->forceDelete();

		$this->actingAs($admin)
			->get(route('topics.edit', ['topic' => $id]))
			->assertNotFound();
	}

	public function testEditSpecialSettingsHttp()
	{
		$user = User::factory()->create();
		$user->group->manipulate_topic = false;
		$user->group->edit_forum_self_topic = true;
		$user->group->edit_forum_other_user_topic = true;
		$user->push();

		$topic = Topic::factory()->create(['forum_priority' => 0, 'main_priority' => 0])->fresh();

		$name = $this->faker->realText(50);

		$response = $this->actingAs($user)
			->patch(route('topics.update', ['topic' => $topic->id]),
				[
					'name' => $name,
					'description' => $this->faker->realText(50),
					'forum_priority' => 20,
					'main_priority' => 20
				]
			)->assertSessionHasNoErrors()
			->assertRedirect();

		$topic->refresh();

		$this->assertEquals(0, $topic->forum_priority);
		$this->assertEquals(0, $topic->main_priority);
		$this->assertEquals($name, $topic->name);

		$user->group->manipulate_topic = true;
		$user->push();

		$name = $this->faker->realText(50);

		$response = $this->actingAs($user)
			->patch(route('topics.update', ['topic' => $topic->id]),
				[
					'name' => $name,
					'description' => $this->faker->realText(50),
					'forum_priority' => 20,
					'main_priority' => 20
				]
			)->assertSessionHasNoErrors()
			->assertRedirect();

		$topic->refresh();

		$this->assertEquals(20, $topic->forum_priority);
		$this->assertEquals(20, $topic->main_priority);
		$this->assertEquals($name, $topic->name);
	}

	public function testEditLabel()
	{
		$admin = User::factory()->create();
		$admin->group->edit_forum_self_topic = true;
		$admin->group->edit_forum_other_user_topic = true;
		$admin->push();

		$forum = Forum::factory()->create();
		$forum->is_idea_forum = true;
		$forum->save();

		$topic = Topic::factory()->create(['forum_id' => $forum->id]);

		$this->assertNull($topic->label);

		$response = $this->actingAs($admin)
			->patch(route('topics.update', ['topic' => $topic->id]),
				array_merge($forum->toArray(), ['label' => TopicLabelEnum::IdeaOnReview]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$topic->refresh();

		$this->assertEquals(TopicLabelEnum::IdeaOnReview, $topic->label);

		$response = $this->actingAs($admin)
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeText(__('topic.labels.IdeaOnReview'));

		$response = $this->actingAs($admin)
			->patch(route('topics.update', ['topic' => $topic->id]),
				array_merge($forum->toArray(), ['label' => TopicLabelEnum::IdeaImplemented]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$topic->refresh();

		$this->assertEquals(TopicLabelEnum::IdeaImplemented, $topic->label);

		$response = $this->actingAs($admin)
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeText(__('topic.labels.IdeaImplemented'));
	}

	public function testChangeLabel()
	{
		$admin = User::factory()->create();
		$admin->group->edit_forum_self_topic = true;
		$admin->group->edit_forum_other_user_topic = true;
		$admin->group->manipulate_topic = true;
		$admin->push();

		$forum = Forum::factory()->create();
		$forum->is_idea_forum = true;
		$forum->save();

		$topic = Topic::factory()->create(['forum_id' => $forum->id]);

		$this->assertNull($topic->label);

		$response = $this->actingAs($admin)
			->get(route('topics.label.change', ['topic' => $topic->id, 'label' => TopicLabelEnum::IdeaOnReview]),
				array_merge($forum->toArray()))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$topic->refresh();

		$this->assertEquals(TopicLabelEnum::IdeaOnReview, $topic->label);

		$response = $this->actingAs($admin)
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeText(__('topic.labels.IdeaOnReview'));
	}

}
