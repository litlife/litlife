<?php

namespace Tests\Feature\Book;

use App\Book;
use Tests\TestCase;

class BookGetFirstPaidSectionTest extends TestCase
{
	public function testWithOneChapter()
	{
		$book = Book::factory()->on_sale()->with_section()->create();

		$section = $book->sections()->chapter()->first();

		$this->assertTrue($section->is($book->getFirstPaidSection()));
	}

	public function testWithThreeChapters()
	{
		$book = Book::factory()->on_sale()->with_three_sections()->create(['free_sections_count' => 1]);

		$sections = $book->sections()->chapter()->get();

		$this->assertFalse($sections->get(0)->is($book->getFirstPaidSection()));
		$this->assertTrue($sections->get(1)->is($book->getFirstPaidSection()));
		$this->assertFalse($sections->get(2)->is($book->getFirstPaidSection()));
	}
}
