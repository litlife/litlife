<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\Section;
use Tests\TestCase;

class BookFreeFragmentTest extends TestCase
{
	public function testGetFreeFragmentCharactersPercentage()
	{
		$book = new Book();
		$book->characters_count = 1000;
		$book->free_fragment_characters_count = 150;

		$this->assertEquals(15, $book->getFreeFragmentCharactersPercentage());

		$book->free_fragment_characters_count = 1000;

		$this->assertEquals(100, $book->getFreeFragmentCharactersPercentage());

		$book->free_fragment_characters_count = 2000;

		$this->assertEquals(100, $book->getFreeFragmentCharactersPercentage());

		$book->free_fragment_characters_count = 0;

		$this->assertEquals(0, $book->getFreeFragmentCharactersPercentage());

		$book->free_fragment_characters_count = -100;

		$this->assertEquals(0, $book->getFreeFragmentCharactersPercentage());

		$book->characters_count = 0;
		$book->free_fragment_characters_count = 0;

		$this->assertEquals(0, $book->getFreeFragmentCharactersPercentage());
	}

	public function testShowRightCharactersCountIfNoFreeSections()
	{
		$book = Book::factory()->on_sale()->with_section()->create();

		$this->assertEquals(0, $book->getFreeFragmentCharactersPercentage());
		$this->assertEquals(0, $book->getFreeFragmentCharactersCount());
	}

	public function testIfAllChaptersAreFree()
	{
		$book = Book::factory()->on_sale()->with_section()->create();

		$book->free_sections_count = 1;
		$book->save();

		$this->assertEquals(100, $book->getFreeFragmentCharactersPercentage());
		$this->assertEquals($book->characters_count, $book->getFreeFragmentCharactersCount());
	}

	public function testGetFreeFragmentCharactersCountVariable()
	{
		$number = rand(100, 10000);

		$book = new Book();
		$book->free_fragment_characters_count = $number;

		$this->assertEquals($number, $book->getFreeFragmentCharactersCount());
	}

	public function testGetFreeFragmentCharactersCount()
	{
		$book = Book::factory()->create();

		$count1 = rand(1000, 5000);
		$count2 = rand(5000, 10000);

		$section = Section::factory()->accepted()->chapter()->create(['book_id' => $book->id]);
		$section->character_count = $count1;
		$section->save();

		$section2 = Section::factory()->accepted()->chapter()->create(['book_id' => $book->id]);
		$section2->character_count = $count2;
		$section2->save();

		$book->free_sections_count = 1;
		$book->save();
		$book->refreshCharactersCount();
		$book->refresh();

		$this->assertEquals($count1 + $count2, $book->characters_count);

		$this->assertEquals(round(100 * $count1 / ($count1 + $count2), 0),
			$book->getFreeFragmentCharactersPercentage());
	}

	public function testSeeRecommendedMinimumFreeFragmentAsAPercentage()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book_for_sale()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->price = 100;
		$book->ready_status = 'complete';

		$count1 = 1000;
		$count2 = 9000;

		$section = $book->sections()->chapter()->first();
		$section->character_count = $count1;
		$section->save();

		$section2 = Section::factory()->accepted()->chapter()->create(['book_id' => $book->id]);
		$section2->character_count = $count2;
		$section2->save();

		$book->free_sections_count = 1;
		$book->save();
		$book->refreshCharactersCount();
		$book->refresh();

		$this->assertEquals(10000, $book->characters_count);

		$this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk()
			->assertViewHas('freeFragmentCharactersPercentage', 10)
			->assertSeeText(__('book.we_recommend_increasing_the_free_fragment', [
				'percentage' => 10,
				'recommended_minimum_free_fragment_as_a_percentage' => config('litlife.recommended_minimum_free_fragment_as_a_percentage')
			]))
			->assertSeeText(__('book.the_free_fragment_is_percentage', ['percentage' => 10]));
	}

	public function testDontSeeRecommendedMinimumFreeFragmentAsAPercentage()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book_for_sale()->create();

		$user = $author->managers->first()->user;
		$book = $author->books->first();
		$book->create_user()->associate($user);
		$book->price = 100;
		$book->ready_status = 'complete';

		$count1 = 4500;
		$count2 = 5500;

		$section = $book->sections()->chapter()->first();
		$section->character_count = $count1;
		$section->save();

		$section2 = Section::factory()->accepted()->chapter()->create(['book_id' => $book->id]);
		$section2->character_count = $count2;
		$section2->save();

		$book->free_sections_count = 1;
		$book->save();
		$book->refreshCharactersCount();
		$book->refresh();

		$this->assertEquals(10000, $book->characters_count);

		$this->actingAs($user)
			->get(route('books.sales.edit', $book))
			->assertOk()
			->assertViewHas('freeFragmentCharactersPercentage', 45)
			->assertDontSeeText(__('book.we_recommend_increasing_the_free_fragment', [
				'percentage' => 45,
				'recommended_minimum_free_fragment_as_a_percentage' => config('litlife.recommended_minimum_free_fragment_as_a_percentage')
			]))
			->assertSeeText(__('book.the_free_fragment_is_percentage', ['percentage' => 45]));
	}
}