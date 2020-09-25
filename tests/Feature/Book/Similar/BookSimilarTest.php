<?php

namespace Tests\Feature\Book\Similar;

use App\Book;
use App\BookSimilarVote;
use Tests\TestCase;

class BookSimilarTest extends TestCase
{
	public function testVoteCount()
	{
		$book_similar_vote = factory(BookSimilarVote::class)
			->create();

		$this->assertEquals(1, $book_similar_vote->book->similars->first()->sum);

		$book_similar_vote2 = factory(BookSimilarVote::class)
			->create([
				'book_id' => $book_similar_vote->book_id,
				'other_book_id' => $book_similar_vote->other_book_id,
				'vote' => '1'
			]);

		$this->assertEquals(2, $book_similar_vote->book->fresh()->similars->first()->sum);

		$book_similar_vote3 = factory(BookSimilarVote::class)
			->create([
				'book_id' => $book_similar_vote->book_id,
				'other_book_id' => $book_similar_vote->other_book_id,
				'vote' => '-1'
			]);

		$this->assertEquals(1, $book_similar_vote->book->fresh()->similars->first()->sum);
		$this->assertEquals(3, $book_similar_vote->other_book->fresh()->similar_vote()->count());
	}

	public function testSeeSimilarBooks()
	{
		$book = factory(Book::class)
			->create();

		$this->get(route('books.show', $book))
			->assertSeeText(__('book.attach_similar_book'));

		$book->statusPrivate();
		$book->save();

		$this->get(route('books.show', $book))
			->assertDontSeeText(__('book.attach_similar_book'));

		$book->statusAccepted();
		$book->save();
		$book->delete();

		$this->get(route('books.show', $book))
			->assertDontSeeText(__('book.attach_similar_book'));
	}
}
