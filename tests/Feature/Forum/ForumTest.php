<?php

namespace Tests\Feature\Forum;

use App\Enums\TopicLabelEnum;
use App\Forum;
use App\ForumGroup;
use App\Like;
use App\Post;
use App\Topic;
use App\User;
use App\UsersAccessToForum;
use Illuminate\Support\Str;
use Tests\TestCase;

class ForumTest extends TestCase
{
	public function testIndex()
	{
		$forum = factory(Forum::class)
			->create();

		$forum2 = factory(Forum::class)
			->create(['forum_group_id' => $forum->forum_group_id]);

		$response = $this->get(route('forums.index'))
			->assertOk();
	}

	public function testIndexForAdmin()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$response = $this->actingAs($user)
			->get(route('forums.index'))
			->assertOk();
	}

	public function testCantCreateForumIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->add_forum_forum = false;
		$admin->push();

		$this->assertFalse($admin->can('create', Forum::class));
	}

	public function testCanCreateForumIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->add_forum_forum = true;
		$admin->push();

		$this->assertTrue($admin->can('create', Forum::class));
	}

	public function testCantEditForumIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_edit_forum = false;
		$admin->push();

		$forum = factory(Forum::class)->create();

		$this->assertFalse($admin->can('update', $forum));
	}

	public function testCanEditForumIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_edit_forum = true;
		$admin->push();

		$forum = factory(Forum::class)->create();

		$this->assertTrue($admin->can('update', $forum));
	}

	public function testCantDeleteForumIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->delete_forum_forum = false;
		$admin->push();

		$forum = factory(Forum::class)->create();

		$this->assertFalse($admin->can('delete', $forum));

		$forum->delete();

		$this->assertFalse($admin->can('restore', $forum));
	}

	public function testCanDeleteForumIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->delete_forum_forum = true;
		$admin->push();

		$forum = factory(Forum::class)->create();

		$this->assertTrue($admin->can('delete', $forum));

		$forum->delete();

		$this->assertTrue($admin->can('restore', $forum));
	}

	public function testCantChangeOrderForumIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_list_manipulate = false;
		$admin->push();

		$forum = factory(Forum::class)->create();

		$this->assertFalse($admin->can('change_order', $forum));
	}

	public function testCanChangeOrderForumIfHasPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_list_manipulate = true;
		$admin->push();

		$forum = factory(Forum::class)->create();

		$this->assertTrue($admin->can('change_order', $forum));
	}

	public function testViewPermissions()
	{
		$user = factory(User::class)->create();
		$user->push();

		$user2 = factory(User::class)->create();
		$user2->push();

		$forum = factory(Forum::class)->create();
		$forum->private = true;
		$forum->push();

		$usersAccessToForum = new UsersAccessToForum;
		$usersAccessToForum->user_id = $user->id;
		$forum->user_access()->save($usersAccessToForum);

		$this->assertTrue($user->can('view', $forum));
		$this->assertFalse($user2->can('view', $forum));
	}

	public function testCanCreateTopicIfHasPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->add_forum_topic = true;
		$user->push();

		$forum = factory(Forum::class)
			->create(['min_message_count' => 0]);

		$this->assertTrue($user->can('create_topic', $forum));
	}

	public function testCantCreateTopicIfNoPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->add_forum_topic = false;
		$user->push();

		$forum = factory(Forum::class)
			->create(['min_message_count' => 0]);

		$this->assertFalse($user->can('create_topic', $forum));
	}

	public function testCantCreateIfTopicIfIfThereAreNotEnoughMessages()
	{
		$user = factory(User::class)->create();
		$user->group->add_forum_topic = true;
		$user->forum_message_count = 30;
		$user->push();

		$forum = factory(Forum::class)
			->create(['min_message_count' => 40]);

		$this->assertFalse($user->can('create_topic', $forum));
	}

	public function testViewPrivateForumOnForumsIndex()
	{
		$post = factory(Post::class)
			->create();

		$user = $post->create_user;

		$forum_group = factory(ForumGroup::class)
			->create();

		$forum = $post->forum;
		$forum->private = true;
		$forum->forum_group_id = $forum_group->id;
		$forum->save();

		$this->assertFalse($user->can('view', $forum));

		$usersAccessToForum = new UsersAccessToForum;
		$usersAccessToForum->user_id = $user->id;
		$forum->user_access()->save($usersAccessToForum);
		$forum->refresh();

		$this->assertTrue($user->can('view', $forum));

		$response = $this->actingAs($user)
			->get(route('forums.index'))
			->assertSeeText($forum->name);

		$other_user = factory(User::class)
			->create();

		$response = $this->actingAs($other_user)
			->get(route('forums.index'))
			->assertDontSeeText($forum->name);

		$response = $this
			->get(route('forums.index'))
			->assertDontSeeText($forum->name);
	}

	public function testStorePrivateForum()
	{
		$user = factory(User::class)
			->create();
		$user->group->add_forum_forum = true;
		$user->push();

		$other_user = factory(User::class)
			->create();

		$forum_group = factory(ForumGroup::class)
			->create();

		$response = $this->actingAs($user)
			->post(route('forums.store', ['forum_group_id' => $forum_group->id]),
				[
					'name' => $this->faker->realText(80) . ' ' . Str::random(10),
					'description' => $this->faker->realText(200),
					'private' => true,
					'private_users' => [$other_user->id],
					'min_message_count' => 0
				]
			);
		//dump(session());
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('forums.index'));

		$user->refresh();

		$forum = $user->created_forums()->first();

		$this->assertNotNull($forum);

		$this->assertNotNull($forum->user_access->where('user_id', $user->id)->first());
		$this->assertNotNull($forum->user_access->where('user_id', $other_user->id)->first());

		$other_user2 = factory(User::class)
			->create();

		$this->assertNull($forum->user_access->where('user_id', $other_user2->id)->first());
	}

	public function testViewPrivateForum()
	{
		$post = factory(Post::class)
			->create();

		$user = $post->create_user;

		$forum = $post->forum;
		$forum->private = true;
		$forum->save();

		$this->assertFalse($user->can('view', $forum));

		$forum->users_with_access()->sync([$user->id]);
		$forum->refresh();

		$this->assertTrue($user->can('view', $forum));

		$response = $this->actingAs($user)
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeText($forum->name);

		$other_user = factory(User::class)
			->create();

		$response = $this->actingAs($other_user)
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertForbidden();

		$response = $this->get(route('forums.show', ['forum' => $forum->id]))
			->assertForbidden();
	}

	public function testFulltextSearch()
	{
		Forum::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	public function testUpdateAutofixFirstPostInCreatedTopicsHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_edit_forum = true;
		$admin->push();

		$forum = factory(Forum::class)
			->create();

		$this->assertFalse($forum->isAutofixFirstPostInCreatedTopics());

		$response = $this->actingAs($admin)
			->patch(route('forums.update', ['forum' => $forum->id]),
				array_merge($forum->toArray(), ['autofix_first_post_in_created_topics' => '1']))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$forum->refresh();

		$this->assertTrue($forum->isAutofixFirstPostInCreatedTopics());
	}

	public function testAutofixFirstPostInCreatedTopicsEnabled()
	{
		$admin = factory(User::class)->create();
		$admin->group->add_forum_topic = true;
		$admin->push();

		$forum = factory(Forum::class)
			->create(['autofix_first_post_in_created_topics' => true]);

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
		$admin = factory(User::class)->create();
		$admin->group->add_forum_topic = true;
		$admin->push();

		$forum = factory(Forum::class)
			->create(['autofix_first_post_in_created_topics' => false]);

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

	public function testUpdateOrderTopicsBasedOnFixPostLikesHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_edit_forum = true;
		$admin->push();

		$forum = factory(Forum::class)
			->create();

		$this->assertFalse($forum->isOrderTopicsBasedOnFixPostLikes());

		$response = $this->actingAs($admin)
			->patch(route('forums.update', ['forum' => $forum->id]),
				array_merge($forum->toArray(), ['order_topics_based_on_fix_post_likes' => '1']))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$forum->refresh();

		$this->assertTrue($forum->isOrderTopicsBasedOnFixPostLikes());
	}

	public function testOrderTopicsBasedOnFixPostLikes()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_edit_forum = true;
		$admin->push();

		$forum = factory(Forum::class)
			->create([
				'is_idea_forum' => true
			]);

		$this->assertTrue($forum->isIdeaForum());

		$topic = factory(Topic::class)->create(['forum_id' => $forum->id]);
		$post = factory(Post::class)->create(['topic_id' => $topic->id]);
		$topic2 = factory(Topic::class)->create(['forum_id' => $forum->id]);
		$post2 = factory(Post::class)->create(['topic_id' => $topic2->id]);

		$post->fix();
		$post2->fix();

		factory(Like::class)
			->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);

		$this->assertEquals(1, $post->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic->name, $topic2->name]);

		factory(Like::class)->create(['likeable_type' => 'post', 'likeable_id' => $post2->id]);
		factory(Like::class)->create(['likeable_type' => 'post', 'likeable_id' => $post2->id]);

		$this->assertEquals(2, $post2->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic2->name, $topic->name]);

		factory(Like::class)->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);
		factory(Like::class)->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);

		$this->assertEquals(3, $post->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic->name, $topic2->name]);

	}

	public function testIdeaForumEnable()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_edit_forum = true;
		$admin->push();

		$forum = factory(Forum::class)
			->create();

		$this->assertFalse($forum->isIdeaForum());

		$response = $this->actingAs($admin)
			->patch(route('forums.update', ['forum' => $forum->id]),
				array_merge($forum->toArray(), ['is_idea_forum' => '1']))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$forum->refresh();

		$this->assertTrue($forum->isIdeaForum());
	}

	public function testOrderForIdeaForumMostLikes()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_edit_forum = true;
		$admin->push();

		$forum = factory(Forum::class)->create(['is_idea_forum' => true]);

		$this->assertTrue($forum->isIdeaForum());

		$topic = factory(Topic::class)->create(['forum_id' => $forum->id]);
		$post = factory(Post::class)->create(['topic_id' => $topic->id]);
		$topic2 = factory(Topic::class)->create(['forum_id' => $forum->id]);
		$post2 = factory(Post::class)->create(['topic_id' => $topic2->id]);
		$topic3 = factory(Topic::class)->create(['forum_id' => $forum->id]);
		$post3 = factory(Post::class)->create(['topic_id' => $topic3->id]);

		$post->fix();
		$post2->fix();
		$post3->fix();

		factory(Like::class)
			->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);

		$this->assertEquals(1, $post->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic->name, $topic3->name, $topic2->name]);

		factory(Like::class)->create(['likeable_type' => 'post', 'likeable_id' => $post2->id]);
		factory(Like::class)->create(['likeable_type' => 'post', 'likeable_id' => $post2->id]);

		$this->assertEquals(2, $post2->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic2->name, $topic->name, $topic3->name]);

		factory(Like::class)->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);
		factory(Like::class)->create(['likeable_type' => 'post', 'likeable_id' => $post->id]);

		$this->assertEquals(3, $post->fresh()->like_count);

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([$topic->name, $topic2->name, $topic3->name]);
	}

	public function testOrderForIdeaForumIdeaOnReviewIdeaInProgressFirst()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_edit_forum = true;
		$admin->push();

		$forum = factory(Forum::class)->create(['is_idea_forum' => true]);

		$this->assertTrue($forum->isIdeaForum());

		$topic = factory(Topic::class)->create(['forum_id' => $forum->id]);
		$post = factory(Post::class)->create(['topic_id' => $topic->id, 'like_count' => '10']);
		$topic2 = factory(Topic::class)->create(['forum_id' => $forum->id]);
		$post2 = factory(Post::class)->create(['topic_id' => $topic2->id, 'like_count' => '20']);
		$topic3 = factory(Topic::class)->create(['forum_id' => $forum->id]);
		$post3 = factory(Post::class)->create(['topic_id' => $topic3->id, 'like_count' => '30']);
		$topic4 = factory(Topic::class)->create(['forum_id' => $forum->id]);
		$post4 = factory(Post::class)->create(['topic_id' => $topic4->id, 'like_count' => '40']);

		$post->fix();
		$post2->fix();
		$post3->fix();
		$post4->fix();

		$topic->label = TopicLabelEnum::IdeaImplemented;
		$topic->save();

		$topic2->label = TopicLabelEnum::IdeaOnReview;
		$topic2->save();

		$topic3->label = TopicLabelEnum::IdeaRejected;
		$topic3->save();

		$topic4->label = TopicLabelEnum::IdeaInProgress;
		$topic4->save();

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([
				$topic4->name,
				$topic2->name,
				$topic3->name,
				$topic->name
			]);

		$topic->label = TopicLabelEnum::IdeaInProgress;
		$topic->save();

		$topic2->label = TopicLabelEnum::IdeaOnReview;
		$topic2->save();

		$topic3->label = TopicLabelEnum::IdeaOnReview;
		$topic3->save();

		$topic4->label = TopicLabelEnum::IdeaInProgress;
		$topic4->save();

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([
				$topic4->name,
				$topic3->name,
				$topic2->name,
				$topic->name
			]);

		$topic->label = TopicLabelEnum::IdeaRejected;
		$topic->save();

		$topic2->label = TopicLabelEnum::IdeaImplemented;
		$topic2->save();

		$topic3->label = TopicLabelEnum::IdeaRejected;
		$topic3->save();

		$topic4->label = TopicLabelEnum::IdeaImplemented;
		$topic4->save();

		$response = $this
			->get(route('forums.show', ['forum' => $forum->id]))
			->assertOk()
			->assertSeeTextInOrder([
				$topic4->name,
				$topic3->name,
				$topic2->name,
				$topic->name
			]);
	}

	/*
		public function testSeeLabelsIfIdeaForum()
		{
			$admin = factory(User::class)->create();
			$admin->group->forum_edit_forum = true;
			$admin->group->manipulate_topic = true;
			$admin->group->edit_forum_self_topic = true;
			$admin->group->edit_forum_other_user_topic = true;
			$admin->push();

			$forum = factory(Forum::class)->create();

			$topic = factory(Topic::class)
				->create(['forum_id' => $forum->id]);

			$post = factory(Post::class)
				->create(['topic_id' => $topic->id]);

			$this->assertFalse($forum->isIdeaForum());

			$response = $this->actingAs($admin)
				->get(route('forums.show', ['forum' => $forum->id]))
				->assertOk()
				->assertDontSeeText(__('topic.labels.IdeaImplemented'))
				->assertDontSeeText(__('topic.labels.IdeaOnReview'))
				->assertDontSeeText(__('topic.labels.IdeaInProgress'))
				->assertDontSeeText(__('topic.labels.IdeaRejected'));

			$forum->is_idea_forum = true;
			$forum->save();

			$this->assertTrue($forum->isIdeaForum());

			$response = $this->actingAs($admin)
				->get(route('forums.show', ['forum' => $forum->id]))
				->assertOk()
				->assertSeeText(__('topic.labels.IdeaImplemented'))
				->assertSeeText(__('topic.labels.IdeaOnReview'))
				->assertSeeText(__('topic.labels.IdeaInProgress'))
				->assertSeeText(__('topic.labels.IdeaRejected'));
		}
		*/

	public function testViewForumsIfPostDeleted()
	{
		$post = factory(Post::class)
			->states('with_forum_group')
			->create();

		$topic = $post->topic;
		$forum = $topic->forum;
		$group = $forum->group;

		$this->assertNotNull($group);
		$this->assertEquals(1, $topic->post_count);
		$this->assertEquals($post->id, $forum->last_post->id);

		$this->get(route('forums.index'))
			->assertOk()
			->assertSeeText($topic->name);

		$post->delete();
		$topic->refresh();
		$forum->refresh();

		$this->assertEquals(0, $topic->post_count);
		$this->assertNull($forum->last_post);

		$this->get(route('forums.index'))
			->assertOk();

		$this->get(route('forums.show', ['forum' => $forum]))
			->assertOk();
	}

	public function testLastPostIfPostDeleted()
	{
		$forum = factory(Forum::class)
			->create();

		$topic = factory(Topic::class)
			->create(['forum_id' => $forum->id]);

		$topic2 = factory(Topic::class)
			->create(['forum_id' => $forum->id]);

		$post = factory(Post::class)
			->create(['topic_id' => $topic->id]);

		$post2 = factory(Post::class)
			->create(['topic_id' => $topic2->id, 'created_at' => $post->created_at->addSeconds(2)]);

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

	public function testStoreNotPrivateForum()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$forumGroup = factory(ForumGroup::class)
			->create();

		$post = [
			'name' => Str::random(8) . ' ' . $this->faker->realText(90),
			'description' => $this->faker->realText(200),
			'min_message_count' => 0,
			'private' => 0
		];

		$this->actingAs($user)
			->post(route('forums.store', ['forum_group_id' => $forumGroup->id]), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$forum = $forumGroup->forums()->first();

		$this->assertEquals($post['name'], $forum->name);
		$this->assertEquals($post['description'], $forum->description);
		$this->assertEquals(0, $forum->users_with_access()->count());
	}
}
