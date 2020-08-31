<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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

	public function testStoreRouteIsOk()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->add_book = true;
		$user->push();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.fb2'));

		$file = new UploadedFile($tmpFilePath, 'test.fb2', filesize($tmpFilePath), null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect()
			->assertSessionHasNoErrors();

		$book = $user->created_books()->first();

		$response->assertRedirect(route('books.create.description', $book));

		$this->assertNotNull($user->created_books()->first());
		$this->assertNotNull($user->created_book_files()->first());
		$this->assertRegExp('/^test_([A-z0-9]{5})\.fb2\.zip$/iu', $user->created_book_files()->first()->name);

		$this->assertTrue($book->parse->isWait());
		$this->assertEquals($user->id, $book->parse->create_user->id);
		$this->assertEquals($book->title, $book->title_search_helper);
	}

	public function testIsFillDescriptionRouteIsOkIfBookSuccessfullyParsed()
	{
		$book = factory(Book::class)
			->states('private')
			->create();

		$user = $book->create_user;

		$response = $this->actingAs($user)
			->get(route('books.create.description', $book))
			->assertOk();
	}

	public function testFillDescriptionRouteIfBookWaitForParse()
	{
		$book = factory(Book::class)
			->states('private')
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
			->states('private')
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
			->states('private')
			->create();

		$user = $book->create_user;

		$error = ['message' => 'текст ошибки'];

		$book->parse->failed($error);
		$book->parse->save();

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
			->states('with_writer', 'private')
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
			->states('private')
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
			->states('private')
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
			->states('with_writer', 'private')
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

	public function testStoreEpubWithWrongMimeType()
	{
		Storage::fake(config('filesystems.default'));

		$admin = factory(User::class)->states('admin')->create();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/epub_with_zip_mime_type.epub'));

		$file = new UploadedFile($tmpFilePath, 'epub_with_zip_mime_type.epub', filesize($tmpFilePath), null, true);

		$response = $this->actingAs($admin)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect()
			->assertSessionHasNoErrors();

		$book = $admin->created_books()->first();

		$response->assertRedirect(route('books.create.description', $book));

		$this->assertNotNull($admin->created_books()->first());
		$this->assertNotNull($admin->created_book_files()->first());
		$this->assertRegExp('/^epub_with_zip_mime_type_([A-z0-9]{5})\.epub$/iu', $admin->created_book_files()->first()->name);

		$this->assertTrue($book->parse->isWait());
		$this->assertEquals($admin->id, $book->parse->create_user->id);
		$this->assertEquals($book->title, $book->title_search_helper);
	}

	public function testStoreFb3()
	{
		Storage::fake(config('filesystems.default'));

		$admin = factory(User::class)->states('admin')->create();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.fb3'));

		$file = new UploadedFile($tmpFilePath, 'test.fb3', filesize($tmpFilePath), null, true);

		$response = $this->actingAs($admin)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect()
			->assertSessionHasErrors(['file' => __('validation.book_file_extension', ['attribute' => __('book.file')])]);
	}
}
