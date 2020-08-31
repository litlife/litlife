<?php

namespace Tests\Feature\Book;

use App\Author;
use Tests\TestCase;

class BookEditSiLpPublishFieldsPolicyTest extends TestCase
{
	public function testCantIfSiBook()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_si_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$user = $manager->user;

		$this->assertFalse($user->can('editSiLpPublishFields', $book));
	}

	public function testCanIfNotSiNotLpNotPublished()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$manager = $author->managers()->first();
		$book = $author->books()->first();
		$book->is_si = false;
		$book->is_lp = false;
		$book->pi_pub = null;
		$book->pi_city = null;
		$book->pi_year = null;
		$book->pi_isbn = null;
		$book->save();

		$user = $manager->user;

		$this->assertTrue($user->can('editSiLpPublishFields', $book));
	}
}
