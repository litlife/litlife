<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookStoreTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp(); // TODO: Change the autogenerated stub

		Storage::fake(config('filesystems.default'));
	}

	public function testRouteIsOk()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$file = new UploadedFile(__DIR__ . '/Books/test.fb2', 'test.fb2', null, null, true);

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

	public function testEpubWithWrongMimeType()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$file = new UploadedFile(__DIR__ . '/Books/epub_with_zip_mime_type.epub', 'epub_with_zip_mime_type.epub', null, null, true);

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

	public function testFb3()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$file = new UploadedFile(__DIR__ . '/Books/test.fb3', 'test.fb3', null, null, true);

		$response = $this->actingAs($admin)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect()
			->assertSessionHasErrors(['file' => __('validation.book_file_extension', ['attribute' => __('book.file')])]);
	}

	public function testZip()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)
			->states('admin')
			->create();

		$filename = uniqid();

		$file = new UploadedFile(__DIR__ . '/Books/test_95.doc.zip', $filename, null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect()
			->assertSessionHasNoErrors();

		$book_file = $user->created_book_files()->first();
		$book = $user->created_books()->first();

		$response->assertRedirect(route('books.create.description', $book));

		$this->assertNotNull($user->created_books()->first());
		$this->assertNotNull($book_file->first());
		$this->assertRegExp('/^' . $filename . '_([A-z0-9]{5})\.doc.zip$/iu', $book_file->name);
		$this->assertEquals('doc', $book_file->format);

		$activity = $book->activities()->first();

		$this->assertEquals(1, $book->activities()->count());
		$this->assertEquals('created', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testWithoutFileHttp()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)
			->states('admin')
			->create();

		$title = $this->faker->realText(100);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['title' => $title]
			)->assertRedirect()
			->assertSessionHasNoErrors();

		$book = $user->created_books()->first();

		$response->assertRedirect(route('books.create.description', $book));

		$this->assertEquals($title, $book->title);
		$this->assertNull($book->year_writing);
		$this->assertTrue($book->isPrivate());

		$activity = $book->activities()->first();

		$this->assertEquals(1, $book->activities()->count());
		$this->assertEquals('created', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testEpubHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$filename = uniqid();

		$file = new UploadedFile(__DIR__ . '/Books/test.epub', $filename, null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['file' => $file]
			)
			->assertRedirect()
			->assertSessionHasNoErrors();

		$this->assertNotNull($user->created_books()->first());
		$this->assertNotNull($user->created_book_files()->first());
		$this->assertRegExp('/^' . $filename . '_([A-z0-9]{5})\.epub/iu', $user->created_book_files()->first()->name);
	}

	public function testUnsupportFormatError()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$file = new UploadedFile(__DIR__ . '/../images/test.gif', 'test.gif', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.book_file_extension', ['attribute' => __('book.file')])]);
	}

	public function testBrokenZipError()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$file = new UploadedFile(__DIR__ . '/Books/invalid.zip', 'invalid.zip', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.zip', ['attribute' => __('book.file')])]);
	}

	public function testZipUnsupportedFormatInsideError()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$file = new UploadedFile(__DIR__ . '/Books/test.jpeg.zip', 'test.jpeg.zip', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.zip_book_file', ['attribute' => __('book.file')])]);
	}

	public function testZipFb2()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$file = new UploadedFile(__DIR__ . '/Books/test.fb2.zip', 'test.fb2.zip', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasNoErrors();

		$book = $user->created_books()->first();

		$this->assertEquals('fb2', $book->files()->first()->extension);
	}

	public function testEmptyFile()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$tmp = tmpfile();

		$file = new UploadedFile(stream_get_meta_data($tmp)['uri'], 'file.epub', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'), ['file' => $file])
			->assertRedirect()
			->assertSessionHasErrors(['file' => __('validation.min.file', ['attribute' => __('book.file'), 'min' => 1])]);
	}

	public function testZippedEpubHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$filename = uniqid();

		$file = new UploadedFile(__DIR__ . '/Books/test.epub.zip', $filename, null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect()
			->assertSessionHasNoErrors();

		$book_file = $user->created_book_files()->first();

		$this->assertTrue($book_file->size > 0);
		$this->assertTrue($book_file->file_size > 0);

		$this->assertEquals(3631, $book_file->size);
		$this->assertEquals(3631, $book_file->file_size);
	}

	public function testEmptyNameHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$file = new UploadedFile(__DIR__ . '/Books/test.epub', '%.epub', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				[
					'file' => $file
				]
			)
			->assertRedirect();

		$response->assertSessionHasNoErrors();

		$book_file = $user->created_book_files()
			->first();

		$this->assertNotRegExp('/^file_([A-z0-9]{6})\.epub$/iu', $book_file->name);
	}

	public function testInvalidZipCRCError()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->create();

		$file = new UploadedFile(__DIR__ . '/Books/invalid_crc.zip', 'invalid_crc.zip', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store', compact('book')),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.zip_book_file', ['attribute' => __('book.file')])]);
	}

	public function testDocx()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$file = new UploadedFile(__DIR__ . '/Books/test2.docx', 'test2.docx', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				[
					'file' => $file
				]
			)
			->assertRedirect();

		$response->assertSessionHasNoErrors();

		$book_file = $user->created_book_files()
			->first();

		$this->assertEquals('docx', $book_file->format);
	}
}
