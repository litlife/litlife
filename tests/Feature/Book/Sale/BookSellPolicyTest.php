<?php

namespace Tests\Feature\Book\Sale;

use App\Author;
use Tests\TestCase;

class BookSellPolicyTest extends TestCase
{
	public function testSellPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->assertTrue($user->can('sell', $book));

		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->assertFalse($user->can('sell', $book));
	}

	public function testAuthorCantSellIfNotCreatorOfTheBookPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$this->assertFalse($user->can('sell', $book));
	}

	public function testAuthorCanSellIfUserCreatorOfTheBookPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->push();

		$this->assertTrue($user->can('sell', $book));
	}

	public function testAuthorCanTSellIfBookDeletedPolicy()
	{
		$author = factory(Author::class)
			->states('with_author_manager_can_sell', 'with_book')
			->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->push();
		$book->delete();
		$book->refresh();

		$this->assertFalse($user->can('sell', $book));
	}

}
