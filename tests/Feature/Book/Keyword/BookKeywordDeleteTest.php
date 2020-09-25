<?php

namespace Tests\Feature\Book\Keyword;

use App\BookKeyword;
use App\User;
use Tests\TestCase;

class BookKeywordDeleteTest extends TestCase
{
	public function testDelete()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_remove = true;
		$user->push();

		$book_keyword = factory(BookKeyword::class)->create();
		$book_keyword->statusAccepted();
		$book_keyword->save();

		$keyword = $book_keyword->keyword;

		$response = $this->actingAs($user)
			->delete(route('books.keywords.destroy',
				['book' => $book_keyword->book, 'keyword' => $book_keyword->id]))
			->assertOk();

		$this->assertNull($book_keyword->fresh());
		$this->assertNotNull($keyword->fresh());
	}

	public function testIfKeywordPrivate()
	{
		$book_keyword = factory(BookKeyword::class)
			->states('private')
			->create();

		$keyword = $book_keyword->keyword;

		$this->assertTrue($book_keyword->isPrivate());
		$this->assertTrue($book_keyword->keyword->isPrivate());

		$response = $this->actingAs($book_keyword->create_user)
			->delete(route('books.keywords.destroy',
				['book' => $book_keyword->book, 'keyword' => $book_keyword->id]))
			->assertOk();

		$this->assertNull($book_keyword->fresh());
		$this->assertNull($keyword->fresh());
	}

	public function testIfKeywordOnReview()
	{
		$admin = factory(User::class)
			->create();
		$admin->group->book_keyword_remove = true;
		$admin->push();

		$book_keyword = factory(BookKeyword::class)
			->states('on_review')
			->create();

		$keyword = $book_keyword->keyword;

		$book_keyword2 = factory(BookKeyword::class)
			->states('on_review')
			->create(['keyword_id' => $keyword->id]);

		$this->assertTrue($book_keyword->isSentForReview());
		$this->assertTrue($book_keyword2->isSentForReview());
		$this->assertTrue($book_keyword->keyword->isSentForReview());

		$response = $this->actingAs($admin)
			->delete(route('books.keywords.destroy',
				['book' => $book_keyword->book, 'keyword' => $book_keyword->id]))
			->assertOk();

		$response = $this->actingAs($admin)
			->delete(route('books.keywords.destroy',
				['book' => $book_keyword2->book, 'keyword' => $book_keyword2->id]))
			->assertOk();

		$this->assertNull($book_keyword->fresh());
		$this->assertNull($book_keyword2->fresh());
		$this->assertNull($keyword->fresh());
	}
}
