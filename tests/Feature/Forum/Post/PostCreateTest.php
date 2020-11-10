<?php

namespace Tests\Feature\Forum\Post;

use App\Notifications\NewForumReplyNotification;
use App\Notifications\NewPostInSubscribedTopicNotification;
use App\Post;
use App\Topic;
use App\User;
use App\UserTopicSubscription;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PostCreateTest extends TestCase
{
	public function testCantCreateIfForumDeleted()
	{
		$topic = Topic::factory()->create();

		$topic->forum->delete();

		$this->get(route('posts.create', $topic))
			->assertStatus(401);

		$topic->forum->forceDelete();

		$this->get(route('posts.create', $topic))
			->assertStatus(401);
	}

	public function testStoreHttp()
	{
		$user = User::factory()->create();

		$topic = Topic::factory()->create();

		$forum = $topic->forum;

		$text = $this->faker->realText(200);

		$response = $this->actingAs($user)
			->post(route('posts.store', compact('topic')),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$post = $topic->posts()->first();

		$topic->refresh();

		$this->assertNotNull($post);
		$this->assertEquals($text, $post->text);
		$this->assertEquals($post->getCharacterCountInText($text), $post->characters_count);

		$this->assertEquals(1, $topic->post_count);
		$this->assertEquals($post->id, $topic->last_post_id);
		$this->assertEquals($post->created_at, $topic->last_post_created_at);

		$forum->refresh();

		$this->assertEquals(1, $forum->topic_count);
		$this->assertEquals(1, $forum->post_count);
		$this->assertEquals($topic->id, $forum->last_topic_id);
		$this->assertEquals($post->id, $forum->last_post_id);

		$user->refresh();

		$this->assertEquals(1, $user->forum_message_count);
	}

	public function testReplyHttp()
	{
		$user = User::factory()->create();

		$post = Post::factory()->create();

		$topic = $post->topic;

		$response = $this->actingAs($user)
			->post(route('posts.store', ['topic' => $topic, 'parent' => $post]),
				['bb_text' => $this->faker->realText(200)])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$reply = $topic->posts()->latest()->orderBy('id', 'desc')->first();

		$response->assertRedirect(route('posts.go_to', $reply));

		$response = $this->actingAs(User::factory()->create())
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
		$this->assertEquals($post->id, $reply->getTree()[0]);
		$this->assertEquals($post->id, $reply->root->id);
		$this->assertEquals($post->id, $reply->parent->id);

		$this->assertNotNull($reply2);
		$this->assertEquals(2, $reply2->level);
		$this->assertEquals(0, $reply2->children_count);
		$this->assertFalse($reply->isRoot());
		$this->assertEquals($reply->id, $reply2->getTree()[1]);
		$this->assertEquals($post->id, $reply2->root->id);
		$this->assertEquals($reply->id, $reply2->parent->id);

		$this->assertTrue($post->isRoot());
		$this->assertEquals(0, $post->level);
		$this->assertEquals(1, $post->children_count);
	}

	public function testPostCreateHttpOkWithoutParentValue()
	{
		$post = Post::factory()->create();

		$user = User::factory()->create();

		$response = $this->actingAs($user)
			->get(route('posts.create', ['topic' => $post->topic]))
			->assertOk();
	}

	public function testPostCreateHttpOk()
	{
		$post = Post::factory()->create();

		$user = User::factory()->create();

		$response = $this->actingAs($user)
			->get(route('posts.create', ['topic' => $post->topic, 'parent' => $post]))
			->assertOk();
	}

	public function testReplyIfCreateUserParentPostDeleted()
	{
		$user = User::factory()->create();

		$post = Post::factory()->create()
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

	public function testPostToFast()
	{
		$user = User::factory()->create();

		$topic = Topic::factory()->create();

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


	public function testReplyNotificationDatabaseSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$notifiable = User::factory()->create();
		$notifiable->email_notification_setting->forum_reply = false;
		$notifiable->email_notification_setting->db_forum_reply = true;
		$notifiable->push();

		$parent = Post::factory()->create(['create_user_id' => $notifiable->id]);

		$post = Post::factory()->create(['parent' => $parent->id]);

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

	public function testNotifyIfUserTopicSubscription()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$topic = Topic::factory()->create();

		$subscription = UserTopicSubscription::factory()->create([
				'topic_id' => $topic->id
			]);

		$create_user = $subscription->user;

		$subscription2 = UserTopicSubscription::factory()->create([
				'topic_id' => $topic->id
			]);

		$subscribed_user = $subscription2->user;

		$subscription3 = UserTopicSubscription::factory()->create([
				'topic_id' => $topic->id
			]);

		$parent_post_create_user = $subscription3->user;

		$parent_post = Post::factory()->create(['create_user_id' => $parent_post_create_user->id]);

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

	public function testReplyNotificationEmailSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$notifiable = User::factory()->with_confirmed_email()->create();
		$notifiable->email_notification_setting->forum_reply = true;
		$notifiable->email_notification_setting->db_forum_reply = false;
		$notifiable->push();

		$parent = Post::factory()->create(['create_user_id' => $notifiable->id]);

		$post = Post::factory()->create(['parent' => $parent->id]);

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

	public function testReply()
	{
		$post = Post::factory()->create();

		$post2 = Post::factory()->create(['parent' => $post, 'topic_id' => $post->topic_id]);

		$post3 = Post::factory()->create(['parent' => $post2, 'topic_id' => $post->topic_id]);

		$this->assertEquals($post->id, $post2->parent->id);
		$this->assertEquals($post2->id, $post3->parent->id);
		$this->assertEquals($post->id, $post3->root->id);
	}
}
