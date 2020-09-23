<?php

namespace Tests\Feature;

use App\DatabaseNotification;
use App\Like;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class NotificationTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testViewCounter()
	{
		$notification = factory(DatabaseNotification::class)
			->create();

		$notifiable = $notification->notifiable;

		$this->assertEquals(1, $notifiable->getUnreadNotificationsCount());

		$this->actingAs($notifiable)
			->get(route('users.notifications.index', ['user' => $notifiable]))
			->assertOk();

		$this->assertEquals(0, $notifiable->getUnreadNotificationsCount());
	}

	public function testViewForbidden()
	{
		$user = factory(User::class)
			->create();

		$notification = factory(DatabaseNotification::class)
			->create();

		$notifiable = $notification->notifiable;

		$this->assertEquals(1, $notifiable->getUnreadNotificationsCount());

		$this->actingAs($notifiable)
			->get(route('users.notifications.index', ['user' => $user]))
			->assertForbidden();

		$this->assertEquals(1, $notifiable->getUnreadNotificationsCount());
	}

	public function testViewNothinfFound()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.notifications.index', ['user' => $user]))
			->assertOk()
			->assertSeeText(__('notification.nothing_found'));
	}

	public function testRemoveOutdatedNotifications()
	{
		config(['litlife.delete_notifications_in_days' => 7]);

		DatabaseNotification::truncate();

		$this->assertEquals(0, DatabaseNotification::count());

		//NotificationFacade::fake();

		$like = factory(Like::class)
			->create();

		$user = $like->likeable->create_user;
		$user->email_notification_setting->db_like = true;
		$user->push();
		/*
				NotificationFacade::assertSentTo(
					$user,
					NewLikeNotification::class,
					function ($notification, $channels) use ($user, $like) {

						$this->assertEquals(['database'], $channels);

						$data = $notification->toArray($user);

						$this->assertEquals(__('notification.new_like_notification.blog.subject'), $data['title']);
						$this->assertEquals(__('notification.new_like_notification.blog.line', ['userName' => $like->create_user->userName]), $data['description']);

						return $notification->like->id === $like->id;
					}
				);
				*/
		$notification = $user->notifications()->first();

		$this->assertNotNull($notification);
		$this->assertTrue($notification->unread());

		$notification->markAsRead();

		$notification = $user->notifications()->first();

		$this->assertTrue($notification->read());
		$this->assertNotNull($notification->read_at);

		Artisan::call('notifications:delete_outdated');

		$notification = $user->notifications()->first();
		$this->assertNotNull($notification);

		Carbon::setTestNow(now()->addDays(7)->addHour());

		Artisan::call('notifications:delete_outdated');

		$notification = $user->notifications()->first();
		$this->assertNull($notification);
	}
}
