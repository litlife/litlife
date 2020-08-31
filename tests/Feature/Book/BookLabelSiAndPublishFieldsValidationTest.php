<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Http\Requests\StoreBook;
use App\User;
use Tests\TestCase;

class BookLabelSiAndPublishFieldsValidationTest extends TestCase
{
	public function testHasErrorOnUpdate()
	{
		$user = factory(User::class)
			->states('admin')->create();

		$book = factory(Book::class)
			->create();

		$post = [
			'title' => $book->title,
			'is_si' => true,
			'genres' => [$book->genres()->first()->id],
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'pi_pub' => 'text'
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertRedirect();
		//var_dump(session('errors'));
		$response->assertSessionHasErrors([
			'is_si' => __('book.if_the_book_is_marked_as_samizdat_then_the_fields_publisher_city_of_printing_year_of_printing_isbn_must_be_empty')
		]);
	}

	function testTrueIfSiTrueAndPublishFieldsNotEmpty()
	{
		$request = new StoreBook();

		$request->merge([
			'is_si' => true,
			'pi_pub' => 'test'
		]);

		$this->assertTrue($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());

		$request = new StoreBook();

		$request->merge([
			'is_si' => true,
			'pi_pub' => 'test'
		]);

		$this->assertTrue($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());

		$request = new StoreBook();

		$request->merge([
			'is_si' => true,
			'pi_city' => 'test'
		]);

		$this->assertTrue($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());

		$request = new StoreBook();

		$request->merge([
			'is_si' => true,
			'pi_year' => 'test'
		]);

		$this->assertTrue($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());

		$request = new StoreBook();

		$request->merge([
			'is_si' => true,
			'pi_isbn' => 'test'
		]);

		$this->assertTrue($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());
	}

	function testFalseIfSiFalseAndPiBnNotEmpty()
	{
		$request = new StoreBook();

		$request->merge([
			'is_si' => false,
			'pi_bn' => 'test'
		]);

		$this->assertFalse($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());

		$request = new StoreBook();

		$request->merge([
			'is_si' => false,
			'pi_pub' => 'test'
		]);

		$this->assertFalse($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());

		$request = new StoreBook();

		$request->merge([
			'is_si' => false,
			'pi_city' => 'test'
		]);

		$this->assertFalse($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());

		$request = new StoreBook();

		$request->merge([
			'is_si' => false,
			'pi_year' => 'test'
		]);

		$this->assertFalse($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());

		$request = new StoreBook();

		$request->merge([
			'is_si' => false,
			'pi_isbn' => 'test'
		]);

		$this->assertFalse($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());
	}

	function testIsTrueIfPublishCityIsZero()
	{
		$request = new StoreBook();

		$request->merge([
			'is_si' => true,
			'pi_city' => '0'
		]);

		$this->assertTrue($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());
	}

	function testIsFalseIfPublishCityIsNull()
	{
		$request = new StoreBook();

		$request->merge([
			'is_si' => true,
			'pi_bn' => null
		]);

		$this->assertFalse($request->isSiLabelIsTrueAndPublishFieldsIsNotEmpty());
	}
}
