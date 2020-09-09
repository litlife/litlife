<?php

namespace Tests\Feature\Notification;

use App\Book;
use App\Notifications\BookPublishedNotification;
use Tests\TestCase;

class BookPublishedNotificationTest extends TestCase
{
	public function testVia()
	{
		$book = factory(Book::class)
			->states('with_create_user')
			->create();

		$notification = new BookPublishedNotification($book);

		$user = $book->create_user;

		$this->assertEquals(['mail', 'database'], $notification->via($user));
	}

	public function testMailNotification()
	{
		$book = factory(Book::class)
			->states('with_create_user')
			->create();

		$notification = new BookPublishedNotification($book);

		$mail = $notification->toMail($book->create_user);

		$this->assertEquals(__('notification.book_published.subject'), $mail->subject);

		$this->assertEquals(__('notification.book_published.line', [
			'book_title' => $book->title,
			'writers_names' => implode(', ', $book->writers->pluck('name')->toArray())
		]), $mail->introLines[0]);

		$this->assertEquals(__('notification.book_published.action'), $mail->actionText);

		$this->assertEquals(route('books.show', ['book' => $book]), $mail->actionUrl);
	}

	public function testDatabaseNotification()
	{
		$book = factory(Book::class)
			->states('with_create_user', 'with_writer')
			->create();

		$notification = new BookPublishedNotification($book);

		$array = $notification->toArray($book->create_user);

		$this->assertEquals(__('notification.book_published.subject'), $array['title']);

		$this->assertEquals(__('notification.book_published.line', [
			'book_title' => $book->title,
			'writers_names' => implode(', ', $book->writers->pluck('name')->toArray())
		]), $array['description']);

		$this->assertEquals(route('books.show', ['book' => $book]), $array['url']);
	}
}
