<?php

namespace Tests\Feature\Book\Sale;

use App\Author;
use Tests\TestCase;

class BookSaleEditTest extends TestCase
{
	public function testShowIfManagerIsTranslator()
	{
		config(['litlife.minimum_characters_count_before_book_can_be_sold' => 0]);
		config(['litlife.book_price_update_cooldown' => 7]);

		$now = now();

		$author = Author::factory()->with_author_manager_can_sell()->with_book_cover_annotation()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();

		$book->create_user()->associate($user);
		$book->is_si = true;
		$book->is_lp = false;
		$book->sections_count = 3;
		$book->push();

		$this->assertTrue($user->can('sell', $book));
		$this->assertNull($book->price_updated_at);

		$this->actingAs($user)
			->post(route('books.sales.edit', $book),
				[
					'free_sections_count' => 1
				])
			->assertRedirect()
			->assertSessionHasErrors();
	}
}
