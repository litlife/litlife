<?php

namespace Tests\Feature\Book\Sale;

use App\Author;
use Tests\TestCase;

class BookRemoveFromSaleTest extends TestCase
{
	public function testRemoveFromSalePolicy()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;

		$this->assertFalse($user->can('remove_from_sale', $book));

		$author = Author::factory()->with_author_manager()->with_book_for_sale()->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;

		$this->assertFalse($user->can('remove_from_sale', $book));
	}
}
