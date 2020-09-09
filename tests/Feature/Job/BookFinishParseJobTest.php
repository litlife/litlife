<?php

namespace Tests\Feature\Notification;

use App\BookParse;
use App\Jobs\Notification\BookFinishParseJob;
use App\Notifications\BookFinishParseNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookFinishParseJobTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp(); // TODO: Change the autogenerated stub

		Notification::fake();
	}

	public function testSentNotificationIfParseOnlyPages()
	{
		$bookParse = factory(BookParse::class)
			->states('only_pages')
			->create();

		$user = $bookParse->create_user;

		BookFinishParseJob::dispatch($bookParse);

		Notification::assertSentTo(
			$user,
			function (BookFinishParseNotification $notification, $channels) use ($bookParse) {
				return $bookParse->is($notification->book_parse);
			}
		);
	}

	public function testDontSentNotificationIfBookTrashed()
	{
		$bookParse = factory(BookParse::class)
			->states('only_pages')
			->create();

		$bookParse->book->delete();

		$user = $bookParse->create_user;

		BookFinishParseJob::dispatch($bookParse);

		Notification::assertNotSentTo(
			$user,
			BookFinishParseNotification::class
		);
	}

	public function testDontSentNotificationIfNotParseOnlyPages()
	{
		$bookParse = factory(BookParse::class)
			->create();

		$user = $bookParse->create_user;

		BookFinishParseJob::dispatch($bookParse);

		Notification::assertNotSentTo(
			$user,
			BookFinishParseNotification::class
		);
	}

	public function testDontSentNotificationIfParseCreateUserNotFound()
	{
		$bookParse = factory(BookParse::class)
			->states('only_pages')
			->create();

		$bookParse->create_user_id = 0;
		$bookParse->save();

		BookFinishParseJob::dispatch($bookParse);

		Notification::assertNothingSent();
	}
}