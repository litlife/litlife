<?php

namespace Tests\Feature\User\Wall;

use App\Blog;
use App\Like;
use App\Notifications\NewWallMessageNotification;
use App\Notifications\NewWallReplyNotification;
use App\User;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class BlogTest extends TestCase
{
	public function testStoreHttp()
	{
		Notification::fake();

		$user = factory(User::class)
			->states('with_user_permissions')
			->create();

		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('users.blogs.store', ['user' => $user]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$blog = $user->blog()->first();

		$this->assertNotNull($blog);
		$this->assertEquals($blog->getCharacterCountInText($text), $blog->characters_count);
		$this->assertEquals($user->id, $blog->blog_user_id);
		$this->assertEquals($user->id, $blog->create_user_id);
		$this->assertEquals($text, $blog->bb_text);

		Notification::assertNotSentTo(
			[$user], NewWallMessageNotification::class
		);

		Notification::assertNotSentTo(
			[$user], NewWallReplyNotification::class
		);
	}

	public function testDeleteRestoreHttp()
	{
		$blog = factory(Blog::class)
			->create();

		$user = $blog->create_user;
		$owner = $blog->owner;

		$this->actingAs($user)
			->delete(route('users.blogs.destroy', ['user' => $owner, 'blog' => $blog]))
			->assertOk();

		$blog->refresh();

		$this->assertTrue($blog->trashed());

		$this->actingAs($user)
			->delete(route('users.blogs.destroy', ['user' => $owner, 'blog' => $blog]))
			->assertOk();

		$blog->refresh();

		$this->assertFalse($blog->trashed());
	}

	public function testEditHttp()
	{
		$blog = factory(Blog::class)
			->create();

		$user = $blog->create_user;
		$owner = $blog->owner;

		$this->actingAs($user)
			->get(route('users.blogs.edit', ['user' => $owner, 'blog' => $blog]))
			->assertOk();
	}

	public function testUpdateHttp()
	{
		$blog = factory(Blog::class)
			->create();

		$user = $blog->create_user;
		$owner = $blog->owner;
		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->patch(route('users.blogs.update', ['user' => $owner, 'blog' => $blog]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect(route('users.blogs.go', ['user' => $owner, 'blog' => $blog]));

		$blog->refresh();

		$this->assertEquals($text, $blog->bb_text);
	}

	public function testCreate()
	{
		$blog = factory(Blog::class)
			->create([
				'bb_text' => 'text https://domain.com/away?=test text'
			]);

		$this->assertEquals('text <a class="bb" href="/away?url=https%3A%2F%2Fdomain.com%2Faway%3F%3Dtest" target="_blank">https://domain.com/away?=test</a> text', $blog->text);
	}

	public function testFixUnfixPermissions()
	{
		$user = factory(User::class)
			->create();

		$user->group->blog = true;
		$user->push();

		$blog = factory(Blog::class)
			->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id,
			]);

		$this->assertTrue($user->can('fix', $blog));
		$this->assertFalse($user->can('unfix', $blog));

		$blog->fix();
		$blog->fresh();
		$user->fresh();

		$this->assertTrue($blog->isFixed());
		$this->assertFalse($user->can('fix', $blog));
		$this->assertTrue($user->can('unfix', $blog));

		$blog->unfix();
		$blog->fresh();
		$user->fresh();

		$this->assertFalse($blog->isFixed());
		$this->assertTrue($user->can('fix', $blog));
		$this->assertFalse($user->can('unfix', $blog));

	}

	public function testAdminCanFixOrUnfix()
	{
		$user = factory(User::class)->create();

		$blog = factory(Blog::class)
			->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id,
			]);

		$admin = factory(User::class)->create();
		$admin->group->blog_other_user = true;
		$admin->push();

		$this->assertFalse($blog->isFixed());
		$this->assertTrue($admin->can('fix', $blog));
		$this->assertFalse($admin->can('unfix', $blog));

		$blog->fix();
		$blog->fresh();

		$this->assertTrue($blog->isFixed());
		$this->assertFalse($admin->can('fix', $blog));
		$this->assertTrue($admin->can('unfix', $blog));
	}

	public function testFixReply()
	{
		$user = factory(User::class)->create();

		$blog = factory(Blog::class)
			->create([
				'blog_user_id' => $user->id
			]);

		$this->assertFalse($blog->isFixed());
		$this->assertFalse($user->can('fix', $blog));
		$this->assertFalse($user->can('unfix', $blog));

		$reply = factory(Blog::class)
			->create([
				'parent' => $blog->id,
				'create_user_id' => $user->id,
			]);

		$this->assertFalse($reply->isFixed());
		$this->assertFalse($user->can('fix', $reply));
		$this->assertFalse($user->can('unfix', $reply));
	}

	public function testDeleteOrRestorePermissions()
	{
		$user = factory(User::class)
			->create();

		$user->group->blog = true;
		$user->push();

		$blog = factory(Blog::class)
			->create([
				'blog_user_id' => $user->id,
				'create_user_id' => $user->id,
			]);

		$this->assertTrue($user->can('delete', $blog));
		$this->assertFalse($user->can('restore', $blog));

		$blog->delete();
		$blog->fresh();
		$user->fresh();

		$this->assertTrue($blog->trashed());
		$this->assertFalse($user->can('delete', $blog));
		$this->assertTrue($user->can('restore', $blog));

		$blog->restore();
		$blog->fresh();
		$user->fresh();

		$this->assertFalse($blog->trashed());
		$this->assertTrue($user->can('delete', $blog));
		$this->assertFalse($user->can('restore', $blog));
	}

	public function testShowWhoLikes()
	{
		$blog = factory(Blog::class)->create();

		$like = factory(Like::class)->create([
			'likeable_type' => 'blog',
			'likeable_id' => $blog->id
		]);

		$response = $this->actingAs($blog->create_user)
			->get(route('likes.users', ['type' => 'blog', 'id' => $blog->id]))
			->assertOk();
	}

	public function testDeleteAndRestoreRecursive()
	{
		$user = factory(User::class)
			->create();

		$blog = factory(Blog::class)
			->create(['blog_user_id' => $user->id]);

		$blog2 = factory(Blog::class)
			->create(['blog_user_id' => $user->id, 'parent' => $blog->id]);

		$blog3 = factory(Blog::class)
			->create(['blog_user_id' => $user->id, 'parent' => $blog2->id]);

		$blog4 = factory(Blog::class)
			->create(['blog_user_id' => $user->id, 'parent' => $blog3->id]);

		$blog->delete();

		$this->assertSoftDeleted($blog);
		$this->assertSoftDeleted($blog2);
		$this->assertSoftDeleted($blog3);
		$this->assertSoftDeleted($blog4);

		$blog->fresh()->restore();

		$this->assertFalse($blog->fresh()->trashed());
		$this->assertFalse($blog2->fresh()->trashed());
		$this->assertFalse($blog3->fresh()->trashed());
		$this->assertFalse($blog4->fresh()->trashed());
	}

	public function testDeleteAndRestoreRecursiveExceptMessagesDeletedBefore()
	{
		$user = factory(User::class)
			->create();

		$blog = factory(Blog::class)
			->create(['blog_user_id' => $user->id]);

		$blog2 = factory(Blog::class)
			->create(['blog_user_id' => $user->id, 'parent' => $blog->id]);

		$blog3 = factory(Blog::class)
			->create(['blog_user_id' => $user->id, 'parent' => $blog2->id]);

		$blog4 = factory(Blog::class)
			->create(['blog_user_id' => $user->id, 'parent' => $blog3->id]);

		$blog3->delete();

		sleep(1);

		$blog->delete();

		$this->assertSoftDeleted($blog);
		$this->assertSoftDeleted($blog2);
		$this->assertSoftDeleted($blog3);
		$this->assertSoftDeleted($blog4);

		$blog->fresh()->restore();

		$this->assertFalse($blog->fresh()->trashed());
		$this->assertFalse($blog2->fresh()->trashed());
		$this->assertSoftDeleted($blog3);
		$this->assertSoftDeleted($blog4);
	}

	public function testReplyNotificationEmailSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$notifiable = factory(User::class)->states('with_confirmed_email')->create();
		$notifiable->email_notification_setting->wall_reply = true;
		$notifiable->email_notification_setting->db_wall_reply = false;
		$notifiable->push();

		$parent = factory(Blog::class)
			->create(['create_user_id' => $notifiable->id]);

		$blog = factory(Blog::class)
			->create(['parent' => $parent->id]);

		Notification::assertSentTo(
			$notifiable,
			NewWallReplyNotification::class,
			function ($notification, $channels) use ($blog, $notifiable) {
				$this->assertContains('mail', $channels);
				$this->assertNotContains('database', $channels);

				return $notification->blog->id == $blog->id;
			}
		);
	}

	public function testReplyNotificationDatabaseSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$notifiable = factory(User::class)->create();
		$notifiable->email_notification_setting->wall_reply = false;
		$notifiable->email_notification_setting->db_wall_reply = true;
		$notifiable->push();

		$parent = factory(Blog::class)
			->create(['create_user_id' => $notifiable->id]);

		$blog = factory(Blog::class)
			->create(['parent' => $parent->id]);

		Notification::assertSentTo(
			$notifiable,
			NewWallReplyNotification::class,
			function ($notification, $channels) use ($blog, $notifiable) {
				$this->assertNotContains('mail', $channels);
				$this->assertContains('database', $channels);

				$data = $notification->toArray($notifiable);

				$this->assertEquals(__('notification.wall_reply.subject'), $data['title']);
				$this->assertEquals(__('notification.wall_reply.line', ['userName' => $blog->create_user->userName]), $data['description']);
				$this->assertEquals(route('users.blogs.go', ['user' => $blog->owner, 'blog' => $blog]), $data['url']);

				return $notification->blog->id == $blog->id;
			}
		);
	}

	public function testReplyUnreadDatabaseNotificationCount()
	{
		$notifiable = factory(User::class)->create();
		$notifiable->email_notification_setting->wall_reply = false;
		$notifiable->email_notification_setting->db_wall_reply = true;
		$notifiable->push();

		$parent = factory(Blog::class)
			->create(['create_user_id' => $notifiable->id]);

		$blog = factory(Blog::class)
			->create(['parent' => $parent->id]);

		$this->assertEquals(1, $notifiable->getUnreadNotificationsCount());
	}

	public function testCreateNotificationEmailSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$notifiable = factory(User::class)->states('with_confirmed_email')->create();
		$notifiable->email_notification_setting->wall_message = true;
		$notifiable->email_notification_setting->db_wall_message = false;
		$notifiable->push();

		$blog = factory(Blog::class)
			->create(['blog_user_id' => $notifiable->id]);

		Notification::assertSentTo(
			$notifiable,
			NewWallMessageNotification::class,
			function ($notification, $channels) use ($blog, $notifiable) {
				$this->assertContains('mail', $channels);
				$this->assertNotContains('database', $channels);

				return $notification->blog->id == $blog->id;
			}
		);
	}

	public function testCreateNotificationDatabaseSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$notifiable = factory(User::class)->create();
		$notifiable->email_notification_setting->wall_message = false;
		$notifiable->email_notification_setting->db_wall_message = true;
		$notifiable->push();

		$blog = factory(Blog::class)
			->create(['blog_user_id' => $notifiable->id]);

		Notification::assertSentTo(
			$notifiable,
			NewWallMessageNotification::class,
			function ($notification, $channels) use ($blog, $notifiable) {
				$this->assertNotContains('mail', $channels);
				$this->assertContains('database', $channels);

				$data = $notification->toArray($notifiable);

				$this->assertEquals(__('notification.new_wall_message.subject'), $data['title']);
				$this->assertEquals(__('notification.new_wall_message.line', ['userName' => $blog->create_user->userName]), $data['description']);
				$this->assertEquals(route('users.blogs.go', ['user' => $blog->owner, 'blog' => $blog]), $data['url']);

				return $notification->blog->id == $blog->id;
			}
		);
	}

	public function testCreateUnreadDatabaseNotificationCount()
	{
		$notifiable = factory(User::class)->create();
		$notifiable->email_notification_setting->wall_message = false;
		$notifiable->email_notification_setting->db_wall_message = true;
		$notifiable->push();

		$blog = factory(Blog::class)
			->create(['blog_user_id' => $notifiable->id]);

		$this->assertEquals(1, $notifiable->getUnreadNotificationsCount());
	}

	public function testNotificationSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$owner = factory(User::class)->create();
		$create_user = factory(User::class)->create();

		$owner->email_notification_setting->wall_message = true;
		$owner->email_notification_setting->save();

		$blog = factory(Blog::class)
			->create([
				'blog_user_id' => $owner->id,
				'create_user_id' => $create_user->id
			]);

		Notification::assertSentTo([$owner], NewWallMessageNotification::class);
		Notification::assertNotSentTo([$owner], NewWallReplyNotification::class);


		Notification::fake();
		Notification::assertNothingSent();

		$create_user->email_notification_setting->wall_reply = true;
		$create_user->email_notification_setting->save();

		$reply = factory(Blog::class)->make();
		$reply->parent = $blog;
		$reply->save();

		Notification::assertNotSentTo([$create_user], NewWallMessageNotification::class);
		Notification::assertSentTo([$create_user], NewWallReplyNotification::class);
	}

	public function testBBEmpty()
	{
		$blog = factory(Blog::class)
			->create();

		$this->expectException(QueryException::class);

		$blog->bb_text = '';
		$blog->save();
	}

	public function testSeeWallPostOnReview()
	{
		$text = Str::random(32);

		$blog = factory(Blog::class)->create(['bb_text' => $text]);
		$blog->statusSentForReview();
		$blog->save();

		$user = $blog->owner;

		$this->get(route('profile', $user))
			->assertOk()
			->assertSeeText(__('blog.message_on_check'));

		$create_user = $blog->create_user;

		$this->actingAs($create_user)
			->get(route('profile', $user))
			->assertOk()
			->assertSeeText($text);
	}

	public function testApprove()
	{
		$user = factory(User::class)->states('admin')->create();

		$blog = factory(Blog::class)->create();
		$blog->statusSentForReview();
		$blog->save();

		$count = Blog::getCachedOnModerationCount();

		$this->actingAs($user)
			->get(route('blogs.approve', $blog))
			->assertOk();

		$blog->refresh();

		$this->assertTrue($blog->isAccepted());
		$this->assertEquals($count - 1, Blog::getCachedOnModerationCount());
	}

	public function testSeeWallPostOnCheck()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$text = Str::random(32);

		$blog = factory(Blog::class)->create(['bb_text' => $text]);
		$blog->statusSentForReview();
		$blog->save();

		$this->actingAs($user)
			->get(route('wall_posts.on_review'))
			->assertOk()
			->assertSeeText($text);
	}

	public function testGetExternalLinksCount()
	{
		$text = 'текст [url]http://example.com/test[/url] текст [url]http://example.com/test[/url]';

		$blog = factory(Blog::class)
			->create(['bb_text' => $text]);

		$this->assertEquals(2, $blog->getExternalLinksCount($blog->getContent()));

		$text = 'текст [url]' . route('home') . '[/url] текст ';

		$blog = factory(Blog::class)
			->create(['bb_text' => $text]);

		$this->assertEquals(0, $blog->getExternalLinksCount($blog->getContent()));

		$text = 'текст текст';

		$blog = factory(Blog::class)
			->create(['bb_text' => $text]);

		$this->assertEquals(0, $blog->getExternalLinksCount($blog->getContent()));
	}

	public function testAcceptedIfExternalUrlOnSelfWall()
	{
		$user = factory(User::class)->create();

		$text = 'текст [url]http://example.com/test[/url] текст [url]http://example.com/test[/url]';

		$blog = factory(Blog::class)
			->create([
				'create_user_id' => $user->id,
				'blog_user_id' => $user->id,
				'bb_text' => $text
			]);

		$this->assertTrue($blog->fresh()->isAccepted());
	}

	public function testSentForReviewIfExternalUrlOnOtherUserWall()
	{
		$user = factory(User::class)->create();
		$owner = factory(User::class)->create();

		$text = 'текст [url]http://example.com/test[/url] текст [url]http://example.com/test[/url]';

		$blog = factory(Blog::class)
			->create([
				'create_user_id' => $user->id,
				'blog_user_id' => $owner->id,
				'bb_text' => $text
			]);

		$this->assertTrue($blog->fresh()->isSentForReview());
	}

	public function testPerPage()
	{
		$user = factory(User::class)->create();

		$response = $this->get(route('profile', ['user' => $user, 'per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['blogs']->perPage());

		$response = $this->get(route('profile', ['user' => $user, 'per_page' => 200]))
			->assertOk();

		$this->assertEquals(100, $response->original->gatherData()['blogs']->perPage());
	}

	public function testCachedOnModerationCount()
	{
		$count = Blog::getCachedOnModerationCount();

		$user = factory(User::class)
			->create();

		$blog = factory(Blog::class)
			->create([
				'blog_user_id' => $user->id,
				'bb_text' => '[url=https://example.com]https://example.com[/url]'
			]);

		$this->assertEquals($count + 1, Blog::getCachedOnModerationCount());

		$blog->delete();

		$this->assertEquals($count, Blog::getCachedOnModerationCount());

		$blog->restore();

		$this->assertEquals($count + 1, Blog::getCachedOnModerationCount());
	}

	public function testCantReplyIfWallPostOnReview()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$blog = factory(Blog::class)
			->states('sent_for_review')
			->create();

		$this->assertFalse($user->can('reply', $blog));
	}

	public function testDontDownloadExternalOnLike()
	{
		$user = factory(User::class)->create();
		$user->group->like_click = true;
		$user->push();

		$blog = factory(Blog::class)
			->create()
			->fresh();
		$blog->external_images_downloaded = false;
		$blog->save();

		$response = $this->actingAs($user)
			->get(route('likes.store', ['type' => 'blog', 'id' => $blog->id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$blog->refresh();

		$this->assertFalse($blog->external_images_downloaded);
	}
}
