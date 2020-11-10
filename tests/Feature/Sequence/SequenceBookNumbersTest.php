<?php

namespace Tests\Feature\Sequence;

use App\Sequence;
use App\User;
use Tests\TestCase;

class SequenceBookNumbersTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testPageIsOk()
	{
		$user = User::factory()->admin()->create();

		$sequence = Sequence::factory()->with_book()->create();

		$this->actingAs($user)
			->get(route('sequences.book_numbers', $sequence))
			->assertOk()
			->assertSeeText(__('common.save'));
	}

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testStoreIsOk()
	{
		$user = User::factory()->admin()->create();

		$sequence = Sequence::factory()->with_two_books()->create();

		$books = $sequence->books()
			->orderBy('id', 'asc')
			->get();

		$book = $books->get(0);
		$book2 = $books->get(1);

		$array = [
			'numbers' => [
				$book->id => 3,
				$book2->id => 4,
			]
		];

		$this->actingAs($user)
			->post(route('sequences.book_numbers_save', $sequence), $array)
			->assertRedirect(route('sequences.book_numbers', $sequence))
			->assertSessionHasNoErrors()
			->assertSessionHas(['success' => __('sequence.book_numbers_in_the_series_have_been_successfully_changed')]);

		$books = $sequence->books()
			->orderBy('id', 'asc')
			->get();

		$book = $books->get(0);
		$book2 = $books->get(1);

		$this->assertEquals(3, $book->pivot->number);
		$this->assertEquals(4, $book2->pivot->number);
	}

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testPageIfSequenceHasNoBooks()
	{
		$user = User::factory()->admin()->create();

		$sequence = Sequence::factory()->create();

		$this->actingAs($user)
			->get(route('sequences.book_numbers', $sequence))
			->assertOk()
			->assertSeeText(__('sequence.there_are_no_books_in_the_series'))
			->assertSeeText(__('sequence.go_back_to_the_series_page'));
	}

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testStoreIfSequenceHasNoBooks()
	{
		$user = User::factory()->admin()->create();

		$sequence = Sequence::factory()->create();

		$this->actingAs($user)
			->post(route('sequences.book_numbers_save', $sequence))
			->assertRedirect(route('sequences.book_numbers', $sequence))
			->assertSessionHasNoErrors()
			->assertSessionMissing(['success' => __('sequence.book_numbers_in_the_series_have_been_successfully_changed')]);

	}
}
