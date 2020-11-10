<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Http\Requests\StoreBook;
use App\User;
use Tests\TestCase;

class BookLabelsSiAndLpFieldsValidationTest extends TestCase
{
	public function testHasErrorOnUpdate()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->with_genre()->create();

		$post = [
			'title' => $book->title,
			'is_si' => true,
			'is_lp' => true,
			'genres' => [$book->genres()->first()->id],
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertRedirect();
		//var_dump(session('errors'));
		$response->assertSessionHasErrors([
			'is_si' => __('book.set_the_label_either_samizdat_or_amateur_translation')
		]);
	}

	function testIsSiAndLp()
	{
		$request = new StoreBook();

		$request->merge([
			'is_si' => true,
			'is_lp' => true
		]);

		$this->assertTrue($request->isSiAndLp());

		$request = new StoreBook();

		$request->merge([
			'is_si' => true,
			'is_lp' => false
		]);

		$this->assertFalse($request->isSiAndLp());

		$request = new StoreBook();

		$request->merge([
			'is_si' => false,
			'is_lp' => true
		]);

		$this->assertFalse($request->isSiAndLp());

		$request = new StoreBook();

		$request->merge([
			'is_si' => false,
			'is_lp' => false
		]);

		$this->assertFalse($request->isSiAndLp());
	}
}
