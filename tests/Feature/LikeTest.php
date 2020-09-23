<?php

namespace Tests\Feature;

use App\Blog;
use App\Book;
use App\Collection;
use App\Like;
use App\Notifications\NewLikeNotification;
use App\Post;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LikeTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testShowUsersHttp()
	{
		$like = factory(Like::class)
			->create()
			->fresh();

		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('likes.users', ['type' => $like->likeable_type, 'id' => $like->likeable_id]))
			->assertOk()
			->assertSeeText($like->create_user->nick);
	}

	public function testDontSeeDeletedLikeUsersHttp()
	{
		$like = factory(Like::class)
			->create()
			->fresh();
		$like->delete();

		$this->assertSoftDeleted($like);

		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('likes.users', ['type' => $like->likeable_type, 'id' => $like->likeable_id]))
			->assertOk()
			->assertDontSeeText($like->create_user->nick);
	}

	public function testAddLikeHttp()
	{
		$user = factory(User::class)
			->create();
		$user->group->like_click = true;
		$user->push();

		$blog = factory(Blog::class)
			->create()
			->fresh();

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'blog', 'id' => $blog->id]));

		$response->assertSessionHasNoErrors()
			->assertOk();

		$blog->refresh();

		$this->assertEquals(1, $user->fresh()->likes()->count());
		$this->assertEquals(1, $blog->like_count);
	}

	public function testRestoreLikeHttp()
	{
		$like = factory(Like::class)->create();
		$like->delete();

		$blog = $like->likeable;

		$user = $like->create_user;
		$user->group->like_click = true;
		$user->push();

		$this->assertEquals(0, $blog->like_count);
		$this->assertSoftDeleted($like);

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'blog', 'id' => $blog->id]))
			->assertOk();

		$blog->refresh();
		$like->refresh();

		$this->assertEquals(1, $blog->like_count);
		$this->assertFalse($like->trashed());
	}

	public function testCantAddLikeToSelfItemsHttp()
	{
		$user = factory(User::class)
			->create();
		$user->group->like_click = true;
		$user->push();

		$blog = factory(Blog::class)
			->create(['create_user_id' => $user->id])
			->fresh();

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'blog', 'id' => $blog->id]));

		$response->assertSessionHasNoErrors()
			->assertOk();

		$this->assertEquals(0, $user->fresh()->likes()->count());
		$this->assertEquals(0, $blog->fresh()->likes()->count());
	}

	public function testCanRemoveLikeToSelfItemsHttp()
	{
		$user = factory(User::class)->create();
		$user->group->like_click = true;
		$user->push();

		$like = factory(Like::class)
			->states('blog')
			->create(['create_user_id' => $user->id]);

		$blog = $like->likeable;
		$blog->create_user_id = $like->create_user_id;
		$blog->save();

		$this->assertEquals(1, $blog->fresh()->likes()->count());

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'blog', 'id' => $blog->id]));

		$like->refresh();

		$response->assertOk()
			->assertJsonFragment(['like' => $like->toArray()]);

		$this->assertTrue($like->trashed());
		$this->assertEquals(0, $blog->fresh()->likes()->count());
	}

	public function testNotificationSend()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->group->like_click = true;
		$user->push();

		$blog = factory(Blog::class)
			->create()
			->fresh();

		$notifiable = $blog->create_user;

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'blog', 'id' => $blog->id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$like = $blog->likes()->first();

		Notification::assertSentTo(
			$notifiable,
			NewLikeNotification::class,
			function ($notification, $channels) use ($like, $blog, $notifiable, $user) {

				$this->assertEquals(['database'], $channels);

				$data = $notification->toArray($notifiable);

				$this->assertEquals(__('notification.new_like_notification.blog.subject'), $data['title']);
				$this->assertEquals(__('notification.new_like_notification.blog.line', ['userName' => $user->userName]), $data['description']);
				$this->assertEquals(route('users.blogs.go', ['user' => $blog->owner, 'blog' => $blog]), $data['url']);

				return $notification->like->id === $like->id;
			}
		);
	}

	public function testCommentLikeNotificationSend()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->group->like_click = true;
		$user->push();

		$post = factory(Post::class)
			->create()
			->fresh();

		$notifiable = $post->create_user;

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'post', 'id' => $post->id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$like = $post->likes()->first();

		Notification::assertSentTo(
			$notifiable,
			NewLikeNotification::class,
			function ($notification, $channels) use ($like, $post, $notifiable, $user) {

				$this->assertEquals(['database'], $channels);

				$data = $notification->toArray($notifiable);

				$this->assertEquals(__('notification.new_like_notification.post.subject'), $data['title']);
				$this->assertEquals(__('notification.new_like_notification.post.line', ['userName' => $user->userName]), $data['description']);
				$this->assertEquals(route('posts.go_to', ['post' => $post]), $data['url']);

				return $notification->like->id === $like->id;
			}
		);
	}

	public function testBookLikeNotificationSend()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->group->like_click = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_create_user')
			->create()
			->fresh();

		$notifiable = $book->create_user;

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'book', 'id' => $book->id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$like = $book->likes()->first();

		Notification::assertSentTo(
			$notifiable,
			NewLikeNotification::class,
			function ($notification, $channels) use ($like, $book, $notifiable, $user) {

				$this->assertEquals(['database'], $channels);

				$data = $notification->toArray($notifiable);

				$this->assertEquals(__('notification.new_like_notification.book.subject'), $data['title']);
				$this->assertEquals(__('notification.new_like_notification.book.line', [
					'userName' => $user->userName,
					'book_title' => $book->title
				]),
					$data['description']);
				$this->assertEquals(route('books.show', ['book' => $book]), $data['url']);

				return $notification->like->id === $like->id;
			}
		);
	}

	public function testNotificationDontSend()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->group->like_click = true;
		$user->push();

		$blog = factory(Blog::class)
			->create()
			->fresh();

		$notifiable = $blog->create_user;
		$notifiable->email_notification_setting->db_like = false;
		$notifiable->push();

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'blog', 'id' => $blog->id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$like = $blog->likes()->first();

		Notification::assertSentTo(
			$notifiable,
			NewLikeNotification::class,
			function ($notification, $channels) use ($like, $blog, $notifiable, $user) {

				$this->assertEquals([], $channels);

				return $notification->like->id === $like->id;
			}
		);
	}

	public function testCollectionLikeNotificationSend()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->group->like_click = true;
		$user->push();

		$collection = factory(Collection::class)
			->create()
			->fresh();

		$notifiable = $collection->create_user;

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 18, 'id' => $collection->id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$like = $collection->likes()->first();

		Notification::assertSentTo(
			$notifiable,
			NewLikeNotification::class,
			function ($notification, $channels) use ($like, $collection, $notifiable, $user) {

				$this->assertEquals(['database'], $channels);

				$data = $notification->toArray($notifiable);

				$this->assertEquals(__('notification.new_like_notification.collection.subject'), $data['title']);

				$this->assertEquals(__('notification.new_like_notification.collection.line', [
					'userName' => $user->userName,
					'collection_title' => $collection->title
				]), $data['description']);

				$this->assertEquals(route('collections.show', ['collection' => $collection]), $data['url']);

				return $notification->like->id === $like->id;
			}
		);
	}

	public function testPreventDuplicate()
	{
		Notification::fake();

		$user = factory(User::class)->create();
		$user->group->like_click = true;
		$user->push();

		$blog = factory(Blog::class)
			->create()
			->fresh();

		$notifiable = $blog->create_user;
		$notifiable->email_notification_setting->db_like = false;
		$notifiable->push();

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'blog', 'id' => $blog->id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$like = $blog->likes()->first();

		Notification::assertSentTo(
			$notifiable,
			NewLikeNotification::class,
			function ($notification, $channels) use ($like, $blog, $notifiable, $user) {

				$this->assertEquals([], $channels);

				return $notification->like->id === $like->id;
			}
		);

		Notification::fake();

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'blog', 'id' => $blog->id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$this->assertTrue($like->fresh()->trashed());

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'blog', 'id' => $blog->id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$this->assertFalse($like->fresh()->trashed());

		Notification::assertNotSentTo(
			[$user], NewLikeNotification::class
		);
	}

	public function testTooltip()
	{
		$like = factory(Like::class)
			->create();

		$likeable = $like->likeable;

		$response = $this->get(route('likes.tooltip', ['type' => $like->likeable_type, 'id' => $like->likeable_id]))
			->assertOk();

		$this->assertStringContainsString($like->create_user->userName, $response->getContent());
	}

	public function testTooltipIfUserDeleted()
	{
		$like = factory(Like::class)
			->create();

		$like->create_user->delete();

		$response = $this->get(route('likes.tooltip', ['type' => $like->likeable_type, 'id' => $like->likeable_id]))
			->assertOk();

		$this->assertStringContainsString(__('user.deleted'), $response->getContent());
	}
}
