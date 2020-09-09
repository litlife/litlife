<?php

namespace Tests\Feature\Book\Comment;

use App\Book;
use App\User;
use Tests\TestCase;

class BookCommentTest extends TestCase
{
	public function testOpenCommentsHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->create(['comments_closed' => true]);

		$this->assertTrue($book->comments_closed);

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('books.open_comments', $book))
			->assertOk()
			->assertSeeText(__('book.comments_opened'));

		$book->refresh();
		$this->assertFalse($book->comments_closed);

		$this->assertEquals(1, $book->activities()->count());
		$activity = $book->activities()->first();
		$this->assertEquals('comments_open', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testCloseCommentsHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->states('administrator')->create();

		$book = factory(Book::class)->create(['comments_closed' => false]);

		$this->assertFalse($book->comments_closed);

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('books.close_comments', $book))
			->assertOk()
			->assertSeeText(__('book.comments_closed'));

		$book->refresh();
		$this->assertTrue($book->comments_closed);

		$this->assertEquals(1, $book->activities()->count());
		$activity = $book->activities()->first();
		$this->assertEquals('comments_close', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

}
