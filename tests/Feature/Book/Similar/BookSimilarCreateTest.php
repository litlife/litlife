<?php

namespace Tests\Feature\Book\Similar;

use App\Book;
use App\User;
use Tests\TestCase;

class BookSimilarCreateTest extends TestCase
{
	public function testCreateHttp()
	{
		$book = Book::factory()->create();
		$book2 = Book::factory()->create();

		$user = User::factory()->create();
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

	public function testCreateAddSameBookHttp()
	{
		$book = Book::factory()->create();

		$user = User::factory()->create();
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
}
