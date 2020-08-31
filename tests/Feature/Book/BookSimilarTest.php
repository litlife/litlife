<?php

namespace Tests\Feature\Book;

use App\Book;
use App\BookSimilarVote;
use App\User;
use Tests\TestCase;

class BookSimilarTest extends TestCase
{
	public function testCreateHttp()
	{
		$book = factory(Book::class)->create();
		$book2 = factory(Book::class)->create();

		$user = factory(User::class)->create();
		$user->group->book_similar_vote = true;
		$user->push();

		$response = $this->actingAs($user)
			->json('POST', route('books.similar.create', ['book' => $book->id]), ['book_id' => $book2->id])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();
		$book2->refresh();

		$this->assertNull($book->similar_vote()->first());

		$book_similar_vote = $book2->similar_vote()
			->where('book_id', $book->id)
			->where('create_user_id', $user->id)
			->first();

		$this->assertNotNull($book_similar_vote);
		$this->assertEquals(1, $book_similar_vote->vote);
	}

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

	public function testVoteHttp()
	{
		$book = factory(Book::class)->create();
		$book2 = factory(Book::class)->create();

		$user = factory(User::class)->create();
		$user->group->book_similar_vote = true;
		$user->push();

		$response = $this->actingAs($user)
			->get(
				route('books.similar.vote', [
					'book' => $book->id,
					'otherBook' => $book2->id,
					'vote' => 1
				]));

		$response->assertSessionHasNoErrors()
			->assertStatus(201);

		$this->assertEquals(1, $book->fresh()->similars->first()->sum);

		$response = $this->actingAs($user)
			->get(
				route('books.similar.vote', [
					'book' => $book->id,
					'otherBook' => $book2->id,
					'vote' => 1
				]))
			->assertSessionHasNoErrors()
			->assertOk();

		$this->assertEquals(0, $book->fresh()->similars->first()->sum);

		$response = $this->actingAs($user)
			->get(
				route('books.similar.vote', [
					'book' => $book->id,
					'otherBook' => $book2->id,
					'vote' => 1
				]))
			->assertSessionHasNoErrors()
			->assertOk();

		$this->assertEquals(1, $book->fresh()->similars->first()->sum);

		$response = $this->actingAs($user)
			->get(
				route('books.similar.vote', [
					'book' => $book->id,
					'otherBook' => $book2->id,
					'vote' => -1
				]))
			->assertSessionHasNoErrors()
			->assertOk();

		$this->assertEquals(-1, $book->fresh()->similars->first()->sum);
	}

	public function testCreateAddSameBookHttp()
	{
		$book = factory(Book::class)->create();

		$user = factory(User::class)->create();
		$user->group->book_similar_vote = true;
		$user->push();

		$response = $this->actingAs($user)
			->json('POST', route('books.similar.create', ['book' => $book->id]), ['book_id' => $book->id])
			->assertStatus(422);

		$response = $this->actingAs($user)
			->get(
				route('books.similar.vote', [
					'book' => $book->id,
					'otherBook' => $book->id,
					'vote' => 1
				]))
			->assertStatus(422);
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
