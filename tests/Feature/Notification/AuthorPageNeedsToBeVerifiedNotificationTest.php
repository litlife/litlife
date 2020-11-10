<?php

namespace Tests\Feature\Notification;

use App\Author;
use App\Notifications\AuthorPageNeedsToBeVerifiedNotification;
use App\User;
use Tests\TestCase;

class AuthorPageNeedsToBeVerifiedNotificationTest extends TestCase
{
	public function testVia()
	{
		$author = Author::factory()->create();

		$user = User::factory()->create();

		$notification = new AuthorPageNeedsToBeVerifiedNotification($author);

		$this->assertEquals(['database'], $notification->via($user));
	}

	public function testDatabaseNotification()
	{
		$author = Author::factory()->create();

		$user = User::factory()->create();

		$notification = new AuthorPageNeedsToBeVerifiedNotification($author);

		$data = $notification->toArray($user);

		$this->assertEquals(__('It looks like you created your author page.'), $data['title']);
		$this->assertEquals(__('If you have created your author page, please do not forget to verify it to get access to editing books and other features'), $data['description']);
		$this->assertEquals(route('authors.verification.request', ['author' => $author]), $data['url']);
	}
}
