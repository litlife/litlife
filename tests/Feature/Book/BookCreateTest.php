<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookCreateTest extends TestCase
{
	public function testCreateRouteIsOk()
	{
		$user = factory(User::class)->create();
		$user->group->add_book = true;
		$user->push();

		$response = $this->actingAs($user)
			->get(route('books.create'))
			->assertOk()
			->assertSeeText(__('book.option1_adding_a_book_with_a_file'))
			->assertSeeText(__('book.option2_add_a_book_without_a_file'))
			->assertSeeText(__('book.file_upload'))
			->assertSeeText(__('book.filling_in_the_description'))
			->assertSeeText(__('book.completion_of_the_addition'));
	}

	public function testIsFillDescriptionRouteIsOkIfBookSuccessfullyParsed()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;

		$response = $this->actingAs($user)
			->get(route('books.create.description', $book))
			->assertOk();
	}

	public function testFillDescriptionRouteIfBookWaitForParse()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;

		$book->parse->wait();
		$book->push();

		$response = $this->actingAs($user)
			->get(route('books.create.description', $book))
			->assertOk()
			->assertSeeText(__('book.parse.wait'));
	}

	public function testInProgress()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;

		$book->parse->start();
		$book->push();

		$response = $this->actingAs($user)
			->get(route('books.create.description', $book))
			->assertOk()
			->assertSeeText(__('book.parse.progress'));
	}

	public function testFailedParsing()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;

		$error = ['message' => 'текст ошибки'];

		$book->parse->failed($error);
		$book->push();

		$response = $this->actingAs($user)
			->get(route('books.create.description', $book))
			->assertOk()
			->assertDontSeeText(__('common.error'))
			->assertSeeText(__('book.parse.failed'))
			->assertDontSeeText($error['message']);
	}

	public function testUpdateRouteIsRedirect()
	{
		$book = factory(Book::class)
			->states('with_writer', 'private', 'with_create_user', 'with_genre')
			->create();

		$user = $book->create_user;

		$post = [
			'title' => $book->title,
			'genres' => [$book->genres()->first()->id],
			'writers' => [$book->authors()->get()->pluck('id')->toArray()],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'ready_status' => 'complete',
			'redirect_success_url' => route('books.create.complete', $book)
		];

		$response = $this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.create.complete', $book));
	}

	public function testCompleteIsOk()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;

		$response = $this->actingAs($user)
			->get(route('books.create.complete', $book))
			->assertOk()
			->assertSeeText(__('book.the_book_was_added_successfully'));
	}

	public function testRedirectToCreateRouteIfBookDeleted()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;

		$book->delete();

		$response = $this->actingAs($user)
			->get(route('books.create.description', $book))
			->assertRedirect(route('books.create'));

		$response = $this->actingAs($user)
			->get(route('books.create.complete', $book))
			->assertRedirect(route('books.create'));
	}

	public function testFillDescriptionSeeSessionErrors()
	{
		$book = factory(Book::class)
			->states('with_writer', 'private', 'with_genre', 'with_create_user')
			->create();
		$book->title = null;
		$book->save();

		$user = $book->create_user;

		$post = [
			'title' => Str::random(8),
			'genres' => [$book->genres()->first()->id],
			'writers' => [$book->authors()->get()->pluck('id')->toArray()],
			'ti_lb' => 'RU',
			'ti_olb' => 'RU',
			'redirect_success_url' => route('books.create.complete', $book)
		];

		$response = $this->actingAs($user)
			->get(route('books.create.description', $book), $post)
			->assertOk();

		$response = $this->actingAs($user)
			->followingRedirects()
			->patch(route('books.update', $book), $post)
			->assertOk()
			->assertSeeText(__('validation.required', ['attribute' => __('book.ready_status')]))
			->assertDontSeeText(__('validation.required', ['attribute' => __('book.title')]));
	}
}
