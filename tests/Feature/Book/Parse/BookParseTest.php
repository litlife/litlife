<?php

namespace Tests\Feature\Book\Parse;

use App\Book;
use App\BookFile;
use App\BookParse;
use App\User;
use Tests\TestCase;

class BookParseTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testInit()
	{
		$book = factory(Book::class)
			->create();
		$book->refresh();

		$this->assertFalse($book->parse->isWait());
		$this->assertFalse($book->parse->isProgress());
		$this->assertTrue($book->parse->isParsed());
		$this->assertFalse($book->parse->isFailed());
		$this->assertTrue($book->parse->isSucceed());
		$this->assertNull($book->parse->getErrors());

		$book->parse->reset();
		$book->refresh();

		$this->assertTrue($book->parse->isReseted());
		$this->assertFalse($book->parse->isWait());
		$this->assertFalse($book->parse->isProgress());
		$this->assertFalse($book->parse->isParsed());
		$this->assertFalse($book->parse->isFailed());
		$this->assertFalse($book->parse->isSucceed());
		$this->assertNull($book->parse->getErrors());

		$book->parse->wait();
		$book->refresh();

		$this->assertFalse($book->parse->isReseted());
		$this->assertTrue($book->parse->isWait());
		$this->assertFalse($book->parse->isProgress());
		$this->assertFalse($book->parse->isParsed());
		$this->assertFalse($book->parse->isFailed());
		$this->assertFalse($book->parse->isSucceed());
		$this->assertNull($book->parse->getErrors());

		$book->parse->start();
		$book->refresh();

		$this->assertFalse($book->parse->isReseted());
		$this->assertFalse($book->parse->isWait());
		$this->assertTrue($book->parse->isProgress());
		$this->assertFalse($book->parse->isParsed());
		$this->assertFalse($book->parse->isFailed());
		$this->assertFalse($book->parse->isSucceed());
		$this->assertNull($book->parse->getErrors());

		$book->parse->success();
		$book->refresh();

		$this->assertFalse($book->parse->isReseted());
		$this->assertFalse($book->parse->isWait());
		$this->assertFalse($book->parse->isProgress());
		$this->assertTrue($book->parse->isParsed());
		$this->assertFalse($book->parse->isFailed());
		$this->assertTrue($book->parse->isSucceed());
		$this->assertNull($book->parse->getErrors());

		$book->parse->reset();
		$book->parse->wait();
		$book->parse->start();
		$book->parse->failed(['error']);
		$book->refresh();

		$this->assertFalse($book->parse->isReseted());
		$this->assertFalse($book->parse->isWait());
		$this->assertFalse($book->parse->isProgress());
		$this->assertFalse($book->parse->isParsed());
		$this->assertTrue($book->parse->isFailed());
		$this->assertFalse($book->parse->isSucceed());
		$this->assertContains('error', $book->parse->getErrors());

		$book->parse->reset();
		$book->refresh();

		$this->assertTrue($book->parse->isReseted());
		$this->assertFalse($book->parse->isWait());
		$this->assertFalse($book->parse->isProgress());
		$this->assertFalse($book->parse->isParsed());
		$this->assertFalse($book->parse->isFailed());
		$this->assertFalse($book->parse->isSucceed());
		$this->assertNull($book->parse->getErrors());
	}

	public function testParseOnlyPages()
	{
		$book = factory(Book::class)
			->create();

		$this->assertFalse($book->parse->isParseOnlyPages());

		$book->parse->wait();
		$book->parse->parseOnlyPages();
		$book->push();

		$this->assertTrue($book->parse->isParseOnlyPages());
	}

	public function testCancelParsePolicy()
	{
		$book = factory(Book::class)
			->create();

		$user = factory(User::class)
			->create();
		$user->group->retry_failed_book_parse = true;
		$user->push();

		$book->parse->wait();

		$this->assertTrue($user->can('cancel_parse', $book));

		$book->parse->start();

		$this->assertFalse($user->can('cancel_parse', $book));

		$book->parse->failed(['error']);

		$this->assertTrue($user->can('cancel_parse', $book));

		$book->parse->success();

		$this->assertFalse($user->can('cancel_parse', $book));
	}

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

	public function testRetryFailedParseHttp()
	{
		$admin = factory(User::class)->states('admin')->create();

		$parse = factory(BookParse::class)->states('failed')->create();

		$book = $parse->book;

		$this->actingAs($admin)
			->get(route('books.retry_failed_parse', ['book' => $book]))
			->assertRedirect();

		$book->refresh();
		$parse = $book->parse;

		$this->assertTrue($parse->isWait());
		$this->assertEquals($admin->id, $parse->create_user->id);
	}

	public function testCantCancelParseIfPrivateBook()
	{
		$book = factory(Book::class)
			->states('with_create_user', 'private')
			->create();

		$book->parse->wait();
		$book->push();

		$this->assertTrue($book->parse->isWait());

		$user = $book->create_user;

		$this->assertFalse($user->can('cancel_parse', $book));
	}

	public function testCancelFailedParseIfBookFileSourceDeleted()
	{
		$admin = factory(User::class)
			->states('admin')->create();

		$parse = factory(BookParse::class)
			->states('failed')
			->create();

		$book = $parse->book;

		$file = factory(BookFile::class)
			->states('txt')
			->create(['book_id' => $book->id, 'source' => true]);

		$this->assertTrue($admin->can('delete', $file));

		$this->actingAs($admin)
			->delete(route('books.files.destroy', ['book' => $book, 'file' => $file]))
			->assertOk();

		$book->refresh();

		$this->assertTrue($book->parse->isSucceed());
	}
}
