<?php

namespace Tests\Feature\Book\Parse;

use App\Book;
use App\User;
use Tests\TestCase;

class BookParseCancelTest extends TestCase
{
	public function testCancelParseHttp()
	{
		$book = factory(Book::class)
			->create();

		$user = factory(User::class)
			->create();
		$user->group->retry_failed_book_parse = true;
		$user->push();

		$book->parse->wait();
		$book->push();
		$this->assertTrue($book->parse->isWait());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('books.cancel_parse', $book))
			->assertOk()
			->assertSeeText(__('book.parse_canceled'));

		$book->refresh();
		$this->assertTrue($book->parse->isSucceed());

		$book->parse->failed(['error']);
		$book->push();
		$this->assertTrue($book->parse->isFailed());

		$this->actingAs($user)
			->followingRedirects()
			->get(route('books.cancel_parse', $book))
			->assertOk()
			->assertSeeText(__('book.parse_canceled'));

		$book->refresh();
		$this->assertTrue($book->parse->isSucceed());
	}
}
