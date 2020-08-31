<?php

namespace Tests\Feature\Forum;

use App\Notifications\NewForumReplyNotification;
use App\Notifications\NewPostInSubscribedTopicNotification;
use App\Post;
use App\Topic;
use App\User;
use App\UsersAccessToForum;
use App\UserTopicSubscription;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PostTest extends TestCase
{
	public function testGetTopicPage()
	{
		$topic = factory(Topic::class)->create([
			'post_desc' => true
		]);

		$posts_on_page = 3;

		$posts_count = round($posts_on_page * 1.5);

		$posts = factory(Post::class, $posts_count)
			->create(['topic_id' => $topic->id]);

		$posts = $topic->postsOrderedBySetting()->get();

		foreach ($posts as $number => $post) {
			//dump($post->id.' '.$post->getTopicPage($posts_on_page));

			$number++;

			if ($number <= $posts_on_page)
				$this->assertEquals(1, $post->getTopicPage($posts_on_page));

			if ($number > $posts_on_page)
				$this->assertEquals(2, $post->getTopicPage($posts_on_page));
		}

		// post desc

		$topic->post_desc = false;
		$topic->push();

		$posts = $topic->postsOrderedBySetting()->get();

		foreach ($posts as $number => $post) {
			//dump($post->id.' '.$post->getTopicPage($posts_on_page));

			$number++;

			if ($number <= $posts_on_page)
				$this->assertEquals(1, $post->getTopicPage($posts_on_page));

			if ($number > $posts_on_page)
				$this->assertEquals(2, $post->getTopicPage($posts_on_page));
		}

	}

	public function testGetTopicPageIfFixedPost()
	{
		$topic = factory(Topic::class)->create([
			'post_desc' => true
		]);

		$posts_on_page = 3;

		$posts_count = round($posts_on_page * 2);

		$posts = factory(Post::class, $posts_count)
			->create(['topic_id' => $topic->id]);

		// fix post

		$fixed_post = $topic->posts()->inRandomOrder()->first();
		$fixed_post->fix();

		$this->assertTrue($fixed_post->isFixed());

		// posts desc order

		$posts = $topic->postsOrderedBySetting()
			->where('id', '!=', $fixed_post->id)
			->get();

		foreach ($posts as $number => $post) {
			//dump($post->id.' '.$post->getTopicPage($posts_on_page));

			$number++;

			if ($number <= $posts_on_page)
				$this->assertEquals(1, $post->getTopicPage($posts_on_page));

			if ($number > $posts_on_page)
				$this->assertEquals(2, $post->getTopicPage($posts_on_page));
		}

		// posts asc order

		$topic->post_desc = false;
		$topic->push();

		$posts = $topic->postsOrderedBySetting()
			->where('id', '!=', $fixed_post->id)
			->get();

		foreach ($posts as $number => $post) {
			$number++;

			if ($number <= $posts_on_page)
				$this->assertEquals(1, $post->getTopicPage($posts_on_page));

			if ($number > $posts_on_page)
				$this->assertEquals(2, $post->getTopicPage($posts_on_page));
		}

	}

	public function testStoreHttp()
	{
		$user = factory(User::class)->create();

		$topic = factory(Topic::class)
			->create()
			->fresh();

		$text = $this->faker->realText(200);

		$response = $this->actingAs($user)
			->post(route('posts.store', compact('topic')),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$post = $topic->posts()->first();
		$topic->refresh();

		$this->assertNotNull($post);

		$this->assertEquals($post->getCharacterCountInText($text), $post->characters_count);

		$this->assertEquals(1, $topic->post_count);
		$this->assertEquals($post->id, $topic->last_post_id);
		$this->assertEquals($post->created_at, $topic->last_post_created_at);

		$forum = $topic->forum;

		$this->assertEquals(1, $forum->topic_count);
		$this->assertEquals(1, $forum->post_count);
		$this->assertEquals($topic->id, $forum->last_topic_id);
		$this->assertEquals($post->id, $forum->last_post_id);
	}

	public function testReplyHttp()
	{
		$user = factory(User::class)->create();

		$post = factory(Post::class)
			->create()
			->fresh();

		$topic = $post->topic;

		$response = $this->actingAs($user)
			->post(route('posts.store', ['topic' => $topic, 'parent' => $post]),
				['bb_text' => $this->faker->realText(200)])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$reply = $topic->posts()->latest()->orderBy('id', 'desc')->first();

		$response->assertRedirect(route('posts.go_to', $reply));

		$response = $this->actingAs(factory(User::class)->create())
			->post(route('posts.store', ['topic' => $topic, 'parent' => $reply]),
				['bb_text' => $this->faker->realText(200)])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$post->refresh();
		$reply->refresh();
		$reply2 = $topic->posts()->latest()->orderBy('id', 'desc')->first();

		$response->assertRedirect(route('posts.go_to', $reply2));

		$this->assertNotNull($reply);
		$this->assertNotEquals($post->id, $reply->id);
		$this->assertEquals(1, $reply->level);
		$this->assertEquals(1, $reply->children_count);
		$this->assertFalse($reply->isRoot());
		$this->assertEquals($post->id, $reply->tree_array[0]);
		$this->assertEquals($post->id, $reply->root->id);
		$this->assertEquals($post->id, $reply->parent->id);

		$this->assertNotNull($reply2);
		$this->assertEquals(2, $reply2->level);
		$this->assertEquals(0, $reply2->children_count);
		$this->assertFalse($reply->isRoot());
		$this->assertEquals($reply->id, $reply2->tree_array[1]);
		$this->assertEquals($post->id, $reply2->root->id);
		$this->assertEquals($reply->id, $reply2->parent->id);

		$this->assertTrue($post->isRoot());
		$this->assertEquals(0, $post->level);
		$this->assertEquals(1, $post->children_count);
	}

	public function testEditHttp()
	{
		$post = factory(Post::class)->create();

		$admin = factory(User::class)
			->states('administrator')
			->create();

		$this->actingAs($admin)
			->get(route('posts.edit', $post))
			->assertOk();
	}

	public function testEditHttpSeeValidationErrors()
	{
		$post = factory(Post::class)->create();

		$admin = factory(User::class)
			->states('administrator')
			->create();

		$this->actingAs($admin)
			->get(route('posts.edit', $post))
			->assertOk();

		$this->actingAs($admin)
			->followingRedirects()
			->patch(route('posts.update', $post))
			->assertSeeText(trans('validation.required', ['attribute' => __('post.bb_text')]));
	}

	public function testUpdateHttp()
	{
		$post = factory(Post::class)
			->create();

		$admin = factory(User::class)
			->states('administrator')
			->create();

		$text = $this->faker->text();

		$this->actingAs($admin)
			->patch(route('posts.update', $post), [
				'bb_text' => $text
			])
			->assertSessionHasNoErrors()
			->assertRedirect(route('posts.go_to', $post));

		$post->refresh();

		$this->assertEquals($text, $post->html_text);
		$this->assertEquals($post->getCharacterCountInText($text), $post->characters_count);
	}

	public function testDeleteHttp()
	{
		$user = factory(User::class)->create();
		$user->group->forum_delete_self_post = true;
		$user->group->forum_delete_other_user_post = true;
		$user->push();

		$post = factory(Post::class)
			->create()
			->fresh();

		$post2 = factory(Post::class)
			->create(['topic_id' => $post->topic_id])
			->fresh();

		$create_user = $post2->create_user;

		$this->assertEquals(1, $create_user->forum_message_count);

		$response = $this->actingAs($user)
			->delete(route('posts.destroy', ['post' => $post2]))
			->assertOk();

		$post2->refresh();
		$create_user->refresh();

		$this->assertTrue($post2->trashed());
		$this->assertEquals(0, $create_user->forum_message_count);

		$topic = $post->topic;

		$this->assertNotNull($post);

		$this->assertEquals($post->id, $topic->last_post_id);
		$this->assertEquals($post->created_at, $topic->last_post_created_at);
		$this->assertEquals(1, $topic->post_count);

		$forum = $topic->forum;

		$this->assertEquals(1, $forum->topic_count);
		$this->assertEquals(1, $forum->post_count);
		$this->assertEquals($topic->id, $forum->last_topic_id);
		$this->assertEquals($post->id, $forum->last_post_id);
	}

	public function testDeleteReply()
	{
		$user = factory(User::class)->create();
		$user->group->forum_delete_self_post = true;
		$user->group->forum_delete_other_user_post = true;
		$user->push();

		$post = factory(Post::class)
			->create()
			->fresh();

		$reply = factory(Post::class)
			->create([
				'parent' => $post->id,
				'topic_id' => $post->topic_id
			])
			->fresh();

		$post->refresh();

		$this->assertEquals(1, $post->children_count);

		$response = $this->actingAs($user)
			->delete(route('posts.destroy', ['post' => $reply]))
			->assertSessionHasNoErrors()
			->assertOk();

		$post->refresh();
		$reply->refresh();

		$this->assertTrue($reply->trashed());

		$this->assertEquals(0, $post->children_count);
	}

	public function testDeleteIfCreateUserDeleted()
	{
		$post = factory(Post::class)
			->create();

		$this->assertEquals(1, $post->create_user->forum_message_count);

		$create_user = $post->create_user;
		$create_user->delete();
		$create_user->refresh();
		$post->refresh();

		$this->assertSoftDeleted($post->create_user);

		$post->delete();
		$post->refresh();
		$create_user->refresh();

		$this->assertTrue($post->trashed());
		$this->assertNotNull($post->deleted_at);

		$this->assertEquals(0, $post->create_user()->any()->first()->forum_message_count);
	}

	public function testDeleteIfTopicDeleted()
	{
		$post = factory(Post::class)
			->create();

		$topic = $post->topic;
		$topic->delete();
		$post->refresh();

		$this->assertTrue($topic->trashed());

		$post->delete();
		$post->refresh();

		$this->assertTrue($post->trashed());
	}

	public function testDeleteIfForumDeleted()
	{
		$post = factory(Post::class)
			->create();

		$forum = $post->forum;
		$forum->delete();
		$post->refresh();

		$this->assertTrue($forum->trashed());

		$post->delete();
		$post->refresh();

		$this->assertTrue($post->trashed());
	}

	public function testTransfer()
	{
		$user = factory(User::class)->create();
		$user->group->forum_move_post = true;
		$user->push();

		$post = factory(Post::class)
			->create()
			->fresh();

		$post2 = factory(Post::class)
			->create(['topic_id' => $post->topic_id])
			->fresh();

		$post3 = factory(Post::class)
			->create()
			->fresh();

		$post4 = factory(Post::class)
			->create(['topic_id' => $post3->topic_id])
			->fresh();

		$topic = $post->topic;
		$topic2 = $post3->topic;
		/*
				dump($post->id);
				dump($post2->id);
				dump($post3->id);
		*/
		$response = $this->actingAs($user)
			->post(route('posts.transfer', ['topic_id' => $topic->id]),
				['posts' => [
					$post2->id,
					$post3->id,
					$post4->id
				]]
			)
			->assertSessionHasNoErrors()
			->assertRedirect(route('topics.show', ['topic' => $topic->id]));

		$topic->refresh();
		$topic2->refresh();
		/*
				dump($post->fresh()->topic_id.' '.$post->fresh()->forum_id);
				dump($post2->fresh()->topic_id.' '.$post2->fresh()->forum_id);
				dump($post3->fresh()->topic_id.' '.$post3->fresh()->forum_id);
		*/
		$this->assertEquals($post4->id, $topic->last_post_id);
		$this->assertEquals($post4->created_at, $topic->last_post_created_at);

		$this->assertEquals(4, $topic->posts()->count());
		$this->assertEquals(4, $topic->post_count);

		$forum = $topic->forum->fresh();

		$this->assertEquals(4, $forum->posts()->count());
		$this->assertEquals(4, $forum->post_count);
		$this->assertEquals(1, $forum->topic_count);

		$this->assertEquals(0, $topic2->posts()->count());
		$this->assertEquals(0, $topic2->post_count);
		$this->assertEquals(1, $topic2->forum->topic_count);

		$this->assertEquals(null, $topic2->last_post_id);
		$this->assertEquals(null, $topic2->last_post_created_at);
	}

	public function testReplyPolicy()
	{
		$user = factory(User::class)->create();
		$user->push();

		$user2 = factory(User::class)->create();
		$user2->push();

		$post = factory(Post::class)->create();
		$post->create_user()->associate($user2);
		$post->push();

		$this->assertTrue($user->can('reply', $post));

		//

		$user = factory(User::class)->create();
		$user->push();

		$post = factory(Post::class)->create();
		$post->create_user()->associate($user);
		$post->push();

		$this->assertFalse($user->can('reply', $post));
	}

	public function testCanEditSelfPostIfUserCreatorAndHasPermissions()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->forum_edit_self_post = true;
		$user->push();

		$post = factory(Post::class)->create();
		$post->create_user()->associate($user);
		$post->push();

		$this->assertTrue($user->can('update', $post));
	}

	public function testCantEditSelfPostIfNoPermissions()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->forum_edit_self_post = false;
		$user->push();

		$post = factory(Post::class)->create();
		$post->create_user()->associate($user);
		$post->push();

		$this->assertFalse($user->can('update', $post));
	}

	public function testCantEditOtherUserIfNoPermissions()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->forum_edit_other_user_post = false;
		$user->push();

		$post = factory(Post::class)->create();

		$this->assertFalse($user->can('update', $post));
	}

	public function testCanEditOtherUserIfHasPermissions()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->forum_edit_other_user_post = true;
		$user->push();

		$post = factory(Post::class)->create();

		$this->assertTrue($user->can('update', $post));
	}

	public function testEditOnlyTimePolicy()
	{
		$user = factory(User::class)->states('with_user_group')->create();
		$user->group->forum_edit_self_post = false;
		$user->push();

		$post = factory(Post::class)->create();
		$post->create_user()->associate($user);
		$post->push();

		$this->assertFalse($user->can('update', $post));

		$user->group->forum_edit_self_post_only_time = true;
		$user->push();

		$this->assertTrue($user->can('update', $post));

		$post->created_at = now()->subMonths(1);
		$post->push();

		$this->assertFalse($user->can('update', $post));
	}

	public function testDeletePolicy()
	{
		$user = factory(User::class)->create();
		$user->group->forum_delete_self_post = false;
		$user->push();

		$user2 = factory(User::class)->create();
		$user2->group->forum_delete_self_post = true;
		$user2->push();

		$post = factory(Post::class)->create();
		$post->create_user()->associate($user);
		$post->topic->post_count = 10;
		$post->push();

		$this->assertFalse($user->can('delete', $post));
		$this->assertFalse($user2->can('delete', $post));

		$user->group->forum_delete_self_post = true;
		$user->push();

		$this->assertTrue($user->can('delete', $post));
		$this->assertFalse($user2->can('delete', $post));

		$user2->group->forum_delete_other_user_post = true;
		$user2->push();

		$this->assertTrue($user2->can('delete', $post));

		//

		$user->group->forum_delete_self_post = true;
		$user->group->forum_delete_other_user_post = true;
		$user->push();

		$post->topic->post_count = 2;
		$post->push();

		$this->assertTrue($user->can('delete', $post));

		$post->topic->post_count = 1;
		$post->push();

		$this->assertTrue($user->can('delete', $post));

		$post->topic->post_count = 10;
		$post->push();
		$post->delete();

		$this->assertFalse($user->can('delete', $post));
		$this->assertTrue($user->can('restore', $post));

		$post->restore();

		$post->topic->post_count = 10;
		$post->push();
		$post->fresh();

		$this->assertTrue($user->can('delete', $post));
		$this->assertFalse($user->can('restore', $post));
	}

	public function testMovePolicy()
	{
		$user = factory(User::class)->create();

		$this->assertFalse($user->can('move', Post::class));

		$user->group->forum_move_post = true;
		$user->push();

		$this->assertTrue($user->can('move', Post::class));
	}

	public function testFixUnfixPolicy()
	{
		$user = factory(User::class)->create();

		$post = factory(Post::class)->create();
		$post->tree = null;
		$post->create_user()->associate($user);
		$post->push();

		$this->assertFalse($user->can('fix', $post));

		$user->group->forum_post_manage = true;
		$user->push();

		$this->assertTrue($user->can('fix', $post));

		$post->tree = ',1,';
		$post->push();
		$post = $post->fresh();

		$this->assertFalse($user->can('fix', $post));

		$post->tree = null;
		$post->save();

		$post->fix();

		$this->assertTrue($post->isFixed());

		$this->assertTrue($user->can('unfix', $post));

		$post->unfix();

		$this->assertFalse($user->can('unfix', $post));
	}

	public function testApprovePolicy()
	{
		$user = factory(User::class)->create();

		$post = factory(Post::class)->create();

		$this->assertFalse($user->can('approve', $post));

		$post->statusSentForReview();
		$post->push();

		$this->assertFalse($user->can('approve', $post));

		$user->group->check_post_comments = true;
		$user->push();

		$this->assertTrue($user->can('approve', $post));
	}

	public function testViewOnCheckPolicy()
	{
		$user = factory(User::class)->create();

		$this->assertFalse($user->can('viewOnCheck', Post::class));

		$user->group->check_post_comments = true;
		$user->push();

		$this->assertTrue($user->can('viewOnCheck', Post::class));
	}

	public function testReply()
	{
		$post = factory(Post::class)
			->create()
			->fresh();

		$post2 = factory(Post::class)
			->create(['parent' => $post, 'topic_id' => $post->topic_id])
			->fresh();

		$post3 = factory(Post::class)
			->create(['parent' => $post2, 'topic_id' => $post->topic_id])
			->fresh();

		$this->assertEquals($post->id, $post2->parent->id);
		$this->assertEquals($post2->id, $post3->parent->id);
		$this->assertEquals($post->id, $post3->root->id);
	}

	public function testViewPrivateMessageOnLatestPosts()
	{
		$post = factory(Post::class)
			->create();

		$forum = $post->forum;
		$forum->private = true;
		$forum->save();

		$usersAccessToForum = new UsersAccessToForum;
		$usersAccessToForum->user_id = $post->create_user_id;
		$forum->user_access()->save($usersAccessToForum);

		$response = $this->actingAs($post->create_user)
			->get(route('home.latest_posts'))
			->assertSeeText($post->text);

		$other_user = factory(User::class)
			->create();

		$response = $this->actingAs($other_user)
			->get(route('home.latest_posts'))
			->assertDontSeeText($post->text);

		$response = $this
			->get(route('home.latest_posts'))
			->assertDontSeeText($post->text);
	}

	public function testViewPrivateMessageOnUserPostsList()
	{
		$post = factory(Post::class)
			->create();

		$forum = $post->forum;
		$forum->private = true;
		$forum->save();

		$usersAccessToForum = new UsersAccessToForum;
		$usersAccessToForum->user_id = $post->create_user_id;
		$forum->user_access()->save($usersAccessToForum);

		$response = $this->actingAs($post->create_user)
			->get(route('users.posts', $post->create_user))
			->assertSeeText($post->text);

		$other_user = factory(User::class)
			->create();

		$response = $this->actingAs($other_user)
			->get(route('users.posts', $post->create_user))
			->assertDontSeeText($post->text);

		$response = $this
			->get(route('users.posts', $post->create_user))
			->assertDontSeeText($post->text);
	}

	public function testMovePostsHttp()
	{
		$user = factory(User::class)->create();
		$user->group->forum_move_post = true;
		$user->push();

		$post = factory(Post::class)
			->create();

		$post2 = factory(Post::class)
			->create();

		$post3 = factory(Post::class)
			->create();

		$this->actingAs($user)
			->get(route('posts.move',
				['ids' => implode(',', [$post->id, $post2->id, $post3->id])]))
			->assertOk();

		$topic = factory(Topic::class)
			->create();

		$response = $this->actingAs($user)
			->post(route('posts.transfer',
				[
					'topic_id' => $topic->id,
					'posts' => [$post->id, $post2->id, $post3->id]
				]))
			->assertRedirect();

		//dump(session('errors'));

		$response->assertSessionHasNoErrors()
			->assertRedirect(route('topics.show', $topic->id));

		$this->assertEquals($topic->id, $post->fresh()->topic_id);
		$this->assertEquals($topic->id, $post2->fresh()->topic_id);
		$this->assertEquals($topic->id, $post3->fresh()->topic_id);
		$this->assertEquals(3, $topic->fresh()->post_count);
		$this->assertEquals(3, $topic->fresh()->forum->post_count);

	}

	public function testFulltextSearch()
	{
		$author = Post::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	public function testOnCheck()
	{
		$user = factory(User::class)->create();
		$user->group->check_post_comments = true;
		$user->push();

		$post = factory(Post::class)->create();
		$post->statusSentForReview();
		$post->save();

		$this->actingAs($user)
			->get(route('posts.on_check'))
			->assertOk()
			->assertSeeText($post->text);
	}

	public function testApprove()
	{
		$user = factory(User::class)->create();
		$user->group->check_post_comments = true;
		$user->push();

		foreach (Post::sentOnReview()->get() as $post) {
			$post->forceDelete();
		}

		$this->assertEquals(0, Post::getCachedOnModerationCount());

		$post = factory(Post::class)->create();
		$post->statusSentForReview();
		$post->save();

		Post::flushCachedOnModerationCount();
		$this->assertEquals(1, Post::getCachedOnModerationCount());

		$this->actingAs($user)
			->get(route('posts.approve', ['post' => $post]))
			->assertOk();

		$this->assertTrue($post->fresh()->isAccepted());

		$this->assertEquals(0, Post::getCachedOnModerationCount());
	}

	public function testDecline()
	{
		$user = factory(User::class)->create();
		$user->group->forum_delete_self_post = true;
		$user->group->forum_delete_other_user_post = true;
		$user->push();

		foreach (Post::sentOnReview()->get() as $post) {
			$post->forceDelete();
		}

		$this->assertEquals(0, Post::getCachedOnModerationCount());

		$post = factory(Post::class)->create();
		$post->statusSentForReview();
		$post->save();

		Post::flushCachedOnModerationCount();
		$this->assertEquals(1, Post::getCachedOnModerationCount());

		$this->actingAs($user)
			->delete(route('posts.destroy', ['post' => $post]))
			->assertOk();

		$this->assertTrue($post->fresh()->trashed());

		$this->assertEquals(0, Post::getCachedOnModerationCount());
	}

	public function testGoToIfOnReview()
	{
		$post = factory(Post::class)->create();
		$post->statusSentForReview();
		$post->save();

		$this->followingRedirects()
			->get(route('posts.go_to', $post))
			->assertOk();
	}

	public function testViewLatestIfOnReview()
	{
		$post = factory(Post::class)->create();
		$post->statusSentForReview();
		$post->save();

		$user = factory(User::class)
			->create();

		$this->actingAs($post->create_user)
			->get(route('home.latest_posts'))
			->assertSeeText($post->text);

		$this->actingAs($user)
			->get(route('home.latest_posts'))
			->assertDontSeeText($post->text);
	}

	public function testViewInTopicIfOnReview()
	{
		$post = factory(Post::class)->create();
		$post->statusSentForReview();
		$post->save();

		$user = factory(User::class)
			->create();

		$this->actingAs($post->create_user)
			->get(route('topics.show', $post->topic))
			->assertSeeText($post->text);

		$this->actingAs($user)
			->get(route('topics.show', $post->topic))
			->assertDontSeeText($post->text)
			->assertSeeText(trans_choice('post.on_check', 1));
	}

	public function testReplyNotificationEmailSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$notifiable = factory(User::class)->states('with_confirmed_email')->create();
		$notifiable->email_notification_setting->forum_reply = true;
		$notifiable->email_notification_setting->db_forum_reply = false;
		$notifiable->push();

		$parent = factory(Post::class)
			->create(['create_user_id' => $notifiable->id]);

		$post = factory(Post::class)
			->create(['parent' => $parent->id]);

		Notification::assertSentTo(
			$notifiable,
			NewForumReplyNotification::class,
			function ($notification, $channels) use ($post, $notifiable) {
				$this->assertContains('mail', $channels);
				$this->assertNotContains('database', $channels);

				return $notification->post->id == $post->id;
			}
		);
	}

	public function testReplyNotificationDatabaseSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$notifiable = factory(User::class)->create();
		$notifiable->email_notification_setting->forum_reply = false;
		$notifiable->email_notification_setting->db_forum_reply = true;
		$notifiable->push();

		$parent = factory(Post::class)
			->create(['create_user_id' => $notifiable->id]);

		$post = factory(Post::class)
			->create(['parent' => $parent->id]);

		Notification::assertSentTo(
			$notifiable,
			NewForumReplyNotification::class,
			function ($notification, $channels) use ($post, $notifiable) {
				$this->assertNotContains('mail', $channels);
				$this->assertContains('database', $channels);

				$data = $notification->toArray($notifiable);

				$this->assertEquals(__('notification.forum_reply.subject'), $data['title']);
				$this->assertEquals(__('notification.forum_reply.line', ['userName' => $post->create_user->userName]), $data['description']);
				$this->assertEquals(route('posts.go_to', ['post' => $post]), $data['url']);

				return $notification->post->id == $post->id;
			}
		);
	}

	public function testPostToFast()
	{
		$user = factory(User::class)
			->create();

		$topic = factory(Topic::class)
			->create();

		$posts = factory(Post::class, 10)
			->create(['create_user_id' => $user->id, 'topic_id' => $topic->id]);

		$response = $this->actingAs($user)
			->post(route('posts.store', compact('topic')),
				['bb_text' => $this->faker->text()])
			->assertRedirect();

		$response->assertSessionHasErrors(['bb_text' => __('post.you_comment_to_fast')]);

		Carbon::setTestNow(now()->addMinutes(11));

		$response = $this->actingAs($user)
			->post(route('posts.store', compact('topic')),
				['bb_text' => $this->faker->text()])
			->assertSessionHasNoErrors()
			->assertRedirect();
	}

	public function testNotifyIfUserTopicSubscription()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$topic = factory(Topic::class)
			->create();

		$subscription = factory(UserTopicSubscription::class)
			->create([
				'topic_id' => $topic->id
			]);

		$create_user = $subscription->user;

		$subscription2 = factory(UserTopicSubscription::class)
			->create([
				'topic_id' => $topic->id
			]);

		$subscribed_user = $subscription2->user;

		$subscription3 = factory(UserTopicSubscription::class)
			->create([
				'topic_id' => $topic->id
			]);

		$parent_post_create_user = $subscription3->user;

		$parent_post = factory(Post::class)
			->create(['create_user_id' => $parent_post_create_user->id]);

		$this->assertNotNull($topic->subscribed_users()->where('user_id', $create_user->id)->first());
		$this->assertNotNull($topic->subscribed_users()->where('user_id', $subscribed_user->id)->first());
		$this->assertNotNull($topic->subscribed_users()->where('user_id', $parent_post_create_user->id)->first());

		$response = $this->actingAs($create_user)
			->post(route('posts.store', ['topic' => $topic, 'parent' => $parent_post]),
				['bb_text' => $this->faker->text()])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$post = $topic->posts()->orderBy('id', 'desc')->get()->first();
		$this->assertNotNull($post);

		Notification::assertSentTo(
			$subscribed_user,
			NewPostInSubscribedTopicNotification::class,
			function ($notification, $channels) use ($post, $subscribed_user, $topic) {

				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$data = $notification->toArray($subscribed_user);
				//dump($data);
				$this->assertEquals(__('notification.new_post_in_subscribed_topic.subject', [
					'topic_title' => $topic->name
				]), $data['title']);

				$this->assertEquals(__('notification.new_post_in_subscribed_topic.line', [
					'user_name' => $post->create_user->userName,
					'topic_title' => $topic->name
				]), $data['description']);

				$this->assertEquals(route('posts.go_to', ['post' => $post]), $data['url']);

				$mail = $notification->toMail($subscribed_user);
				//dump($mail);

				$this->assertEquals(__('notification.greeting') . ', ' . $subscribed_user->userName . '!', $mail->greeting);

				$this->assertEquals(__('notification.new_post_in_subscribed_topic.subject', [
					'topic_title' => $topic->name
				]), $mail->subject);

				$this->assertEquals(__('notification.new_post_in_subscribed_topic.line', [
					'user_name' => $post->create_user->userName,
					'topic_title' => $topic->name
				]), $mail->introLines[0]);

				$this->assertEquals(route('posts.go_to', ['post' => $post]), $mail->actionUrl);

				$this->assertEquals('<a href="' . route('topics.unsubscribe', $post->topic) . '">' .
					__('topic.unsubscribe_from_receiving_notifications_about_new_posts_in_this_topic') .
					'</a>',
					$mail->outroLines[0]);

				return $notification->post->id == $post->id;
			}
		);

		Notification::assertNotSentTo($create_user, NewPostInSubscribedTopicNotification::class);

		Notification::assertNotSentTo($parent_post_create_user, NewPostInSubscribedTopicNotification::class);
	}

	public function testBBEmpty()
	{
		$post = factory(Post::class)
			->create();

		$this->expectException(QueryException::class);

		$post->bb_text = '';
		$post->save();
	}

	public function testUpdateHttpStringContainsAsc194()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$post = factory(Post::class)
			->create(['create_user_id' => $admin->id]);

		$text = '   ';

		$this->assertStringContainsString(chr(194), $text);

		$this->actingAs($admin)
			->patch(route('posts.update', ['post' => $post]), [
				'bb_text' => $text
			])
			->assertRedirect()
			->assertSessionHasErrors(['bb_text' => trans('validation.required', ['attribute' => __('post.bb_text')])]);
	}

	/*
		public function testIsSamePostExists()
		{
			$user = factory(User::class)->create();

			$topic = factory(Topic::class)->create();

			$text = $this->faker->realText(100);

			$this->actingAs($user)
				->post(route('posts.store', ['topic' => $topic->id]), [
					'bb_text' => $text
				])
				->assertSessionHasNoErrors()
				->assertRedirect();

			$this->actingAs($user)
				->post(route('posts.store', ['topic' => $topic->id]), [
					'bb_text' => $text
				])
				->assertSessionHasNoErrors()
				->assertRedirect();

			$this->actingAs($user)
				->post(route('posts.store', ['topic' => $topic->id]), [
					'bb_text' => $text
				])
				->assertSessionHasErrors(['bb_text' => __('post.you_leave_same_posts')]);
		}
		*/
	public function testTransferIfPostsFixed()
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
			->post(route('posts.move'),
				[
					'topic_id' => $topic->id,
					'posts' => [$post2->id]
				])
			->assertSessionHasNoErrors()
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

	public function testReplyIfCreateUserParentPostDeleted()
	{
		$user = factory(User::class)->create();

		$post = factory(Post::class)
			->create()
			->fresh();

		$post->create_user->forceDelete();

		$topic = $post->topic;

		$response = $this->actingAs($user)
			->post(route('posts.store', ['topic' => $topic, 'parent' => $post]),
				['bb_text' => $this->faker->realText(200)])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$reply = $topic->posts()->latest()->orderBy('id', 'desc')->first();

		$response->assertRedirect(route('posts.go_to', $reply));
	}

	public function testPostCreateHttpOkWithoutParentValue()
	{
		$post = factory(Post::class)
			->create();

		$user = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->get(route('posts.create', ['topic' => $post->topic]))
			->assertOk();
	}

	public function testPostCreateHttpOk()
	{
		$post = factory(Post::class)
			->create();

		$user = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->get(route('posts.create', ['topic' => $post->topic, 'parent' => $post]))
			->assertOk();
	}

	public function testPerPage()
	{
		$topic = factory(Topic::class)->create();

		$response = $this->get(route('topics.show', ['topic' => $topic, 'per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['items']->perPage());

		$response = $this->get(route('topics.show', ['topic' => $topic, 'per_page' => 200]))
			->assertOk();

		$this->assertEquals(100, $response->original->gatherData()['items']->perPage());
	}

	public function testPostsCreateIfForumDeleted()
	{
		$topic = factory(Topic::class)
			->create();

		$topic->forum->delete();

		$this->get(route('posts.create', $topic))
			->assertStatus(401);

		$topic->forum->forceDelete();

		$this->get(route('posts.create', $topic))
			->assertStatus(401);
	}

	public function testPostsEditIfTopicDeleted()
	{
		$post = factory(Post::class)
			->create();

		$post->topic->delete();

		$this->get(route('posts.edit', $post))
			->assertStatus(401);

		$post->topic->forceDelete();

		$this->get(route('posts.edit', $post))
			->assertStatus(401);
	}

	public function testCantReplyIfTopicDeleted()
	{
		$post = factory(Post::class)
			->create();

		$topic = $post->topic;

		$topic->delete();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('create_post', $topic));
	}

	public function testCantReplyIfPostOnReview()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$post = factory(Post::class)
			->states('sent_for_review')
			->create();

		$this->assertFalse($user->can('reply', $post));
	}

	public function testCantDeleteFixedPost()
	{
		$post = factory(Post::class)->create();
		$post->fix();

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertFalse($user->can('delete', $post));
	}
}
