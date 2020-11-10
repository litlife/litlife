<?php

namespace Tests\Feature\Book\Comment;

use App\Book;
use App\Comment;
use App\User;
use Tests\TestCase;

class BookCommentOpenCloseTest extends TestCase
{
	public function testOpenCommentsHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = User::factory()->administrator()->create();

		$book = Book::factory()->create(['comments_closed' => true]);

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

		$admin = User::factory()->administrator()->create();

		$book = Book::factory()->create(['comments_closed' => false]);

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

	public function testBookComment()
	{
		$comment = Comment::factory()->create(['commentable_type' => 'book']);

		$this->assertEquals('book', $comment->commentable_type);
		$this->assertEquals('Book', $comment->getCommentableModelName());
		$this->assertTrue($comment->isBookType());
	}
}
