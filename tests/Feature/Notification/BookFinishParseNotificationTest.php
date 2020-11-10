<?php

namespace Tests\Feature\Notification;

use App\BookParse;
use App\Notifications\BookFinishParseNotification;
use App\User;
use Tests\TestCase;

class BookFinishParseNotificationTest extends TestCase
{
	public function testViaUserNotificationEnable()
	{
		$bookParse = BookParse::factory()->create();

		$notification = new BookFinishParseNotification($bookParse);

		$user = User::factory()->create();
		$user->email_notification_setting->db_book_finish_parse = true;
		$user->push();

		$this->assertEquals(['database'], $notification->via($user));
	}

	public function testViaUserNotificationDiable()
	{
		$bookParse = BookParse::factory()->create();

		$notification = new BookFinishParseNotification($bookParse);

		$user = User::factory()->create();
		$user->email_notification_setting->db_book_finish_parse = false;
		$user->push();

		$this->assertEquals([], $notification->via($user));
	}

	public function testDatabaseNotification()
	{
		$user = User::factory()->create();

		$bookParse = BookParse::factory()->create();

		$notification = new BookFinishParseNotification($bookParse);

		$data = $notification->toArray($user);

		$this->assertEquals(__('notification.book_finish_parse.subject', ['title' => $bookParse->book->title]), $data['title']);
		$this->assertEquals(route('books.show', ['book' => $bookParse->book]), $data['url']);
	}
}
