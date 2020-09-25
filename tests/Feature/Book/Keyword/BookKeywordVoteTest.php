<?php

namespace Tests\Feature\Book\Keyword;

use App\Book;
use App\User;
use Tests\TestCase;

class BookKeywordVoteTest extends TestCase
{
	public function testVoteUpRemoveVoteUp()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_vote = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_keyword')
			->create();

		$book_keyword = $book->book_keywords->first();

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '1']))
			->assertStatus(201)
			->assertJsonFragment([
				'vote' => '1',
			]);

		$book_keyword->refresh();

		$this->assertEquals(1, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(1, $vote->vote);

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '1']))
			->assertStatus(200)
			->assertJsonFragment([
				'vote' => 0
			]);

		$book_keyword->refresh();

		$this->assertEquals(0, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(0, $vote->vote);
	}

	public function testVoteUpVoteDown()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_vote = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_keyword')
			->create();

		$book_keyword = $book->book_keywords->first();

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '1']))
			->assertStatus(201)
			->assertJsonFragment([
				'vote' => '1',
			]);

		$book_keyword->refresh();

		$this->assertEquals(1, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(1, $vote->vote);

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '-1']))
			->assertStatus(200)
			->assertJsonFragment([
				'vote' => '-1'
			]);

		$book_keyword->refresh();

		$this->assertEquals(-1, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(-1, $vote->vote);
	}

	public function testVoteDownRemoveVoteDown()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_vote = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_keyword')
			->create();

		$book_keyword = $book->book_keywords->first();

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '-1']))
			->assertStatus(201)
			->assertJsonFragment([
				'vote' => '-1',
			]);

		$book_keyword->refresh();

		$this->assertEquals(-1, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(-1, $vote->vote);

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '-1']))
			->assertStatus(200)
			->assertJsonFragment([
				'vote' => 0
			]);

		$book_keyword->refresh();

		$this->assertEquals(0, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(0, $vote->vote);
	}
}
