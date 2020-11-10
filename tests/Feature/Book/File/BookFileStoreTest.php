<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use App\Jobs\Book\UpdateBookPagesCount;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookFileStoreTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		Storage::fake(config('filesystems.default'));
	}

	public function testStoreHttpIsOk()
	{
		config(['activitylog.enabled' => true]);

		$user = User::factory()->create();
		$user->group->book_file_add = true;
		$user->push();

		$book = Book::factory()->create();

		UpdateBookPagesCount::dispatch($book);

		$book->refresh();

		$comment = $this->faker->realText(100);
		$number = rand(1, 100);

		$file = new UploadedFile(__DIR__ . '/../Books/test.fb2', 'test.fb2', null, null, true);

		$this->actingAs($user)
			->followingRedirects()
			->post(route('books.files.store', compact('book')),
				[
					'file' => $file,
					'comment' => $comment,
					'number' => $number
				]
			)
			->assertOk()
			->assertSeeText(__('book_file.uploaded_successfully'));

		$book_file = $user->created_book_files()
			->first();

		$this->assertNotNull($user->created_book_files()->first());
		$this->assertEquals($comment, $book_file->comment);
		$this->assertEquals($number, $book_file->number);
		$this->assertTrue($book_file->isSentForReview());

		$this->assertEquals(1, $book_file->activities()->count());
		$activity = $book_file->activities()->first();
		$this->assertEquals('created', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);

		$this->assertTrue($book_file->book->parse->isWait());
		$this->assertFalse($book_file->isAutoCreated());
	}

	public function testHttpDontParseWaitedIfSectionsExists()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->accepted()->with_section()->create();

		$this->assertTrue($book->parse->isSucceed());

		$file = new UploadedFile(__DIR__ . '/../Books/test.fb2', 'test.fb2', null, null, true);

		$this->actingAs($user)
			->followingRedirects()
			->post(route('books.files.store', compact('book')), [
				'file' => $file
			])
			->assertOk()
			->assertSeeText(__('book_file.uploaded_successfully'));

		$file = $book->files()
			->first();

		$book->refresh();

		$this->assertTrue($book->parse->isSucceed());
	}

	public function testUnsupportFormatError()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$file = new UploadedFile(__DIR__ . '/../../images/test.gif', 'test.gif', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.book_file_extension', ['attribute' => __('book.file')])]);
	}

	public function testBrokenZipError()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$file = new UploadedFile(__DIR__ . '/../Books/invalid.zip', 'invalid.zip', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.zip', ['attribute' => __('book.file')])]);
	}

	public function testZipUnsupportedFormatInsideError()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$file = new UploadedFile(__DIR__ . '/../Books/test.jpeg.zip', 'test.jpeg.zip', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.zip_book_file', ['attribute' => __('book.file')])]);
	}

	public function testPrivateBook()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create(['create_user_id' => $user->id]);
		$book->statusPrivate();
		$book->save();
		$book->refresh();

		$this->assertTrue($book->isPrivate());

		$comment = $this->faker->realText(100);
		$number = rand(1, 100);

		$file = new UploadedFile(__DIR__ . '/../Books/test.fb2', 'test.fb2', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')),
				[
					'file' => $file,
					'comment' => $comment,
					'number' => $number
				]
			)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$response->assertSessionHasNoErrors();

		$book_file = $user->created_book_files()
			->first();
		$book->refresh();

		$this->assertNotNull($user->created_book_files()->first());
		$this->assertEquals($comment, $book_file->comment);
		$this->assertEquals($number, $book_file->number);
		$this->assertTrue($book->isPrivate());
		$this->assertTrue($book_file->isPrivate());
	}

	public function testAcceptedBookIfCanAddWithoutCheck()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create(['create_user_id' => $user->id]);
		$book->statusAccepted();
		$book->save();
		$book->refresh();

		$this->assertTrue($book->isAccepted());

		$comment = $this->faker->realText(100);
		$number = rand(1, 100);

		$file = new UploadedFile(__DIR__ . '/../Books/test.fb2', 'test.fb2', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')),
				[
					'file' => $file,
					'comment' => $comment,
					'number' => $number
				]
			)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$response->assertSessionHasNoErrors();

		$book_file = $user->created_book_files()
			->first();
		$book->refresh();

		$this->assertNotNull($user->created_book_files()->first());
		$this->assertEquals($comment, $book_file->comment);
		$this->assertEquals($number, $book_file->number);
		$this->assertTrue($book->isAccepted());
		$this->assertTrue($book_file->isAccepted());
	}

	public function testEmptyHttp()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')), ['file' => ''])
			->assertRedirect()
			->assertSessionHasErrors(['file' => __('validation.required', ['attribute' => __('book_file.file')])]);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')))
			->assertRedirect()
			->assertSessionHasErrors(['file' => __('validation.required', ['attribute' => __('book_file.file')])]);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')), ['file' => 'string'])
			->assertRedirect()
			->assertSessionHasErrors(['file' => __('validation.file', ['attribute' => __('book_file.file')])]);
	}

	public function testMinSize()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$tmp = tmpfile();

		$file = new UploadedFile(stream_get_meta_data($tmp)['uri'], 'file.epub', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', ['book' => $book]), ['file' => $file])
			->assertSessionHasErrors(['file' => __('validation.min.file', ['attribute' => __('book_file.file'), 'min' => 1])]);
	}

	public function testFileCommentRequireIfOtherFileWithSameExtensionExists()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$file = BookFile::factory()->odt()->create();

		$this->assertEquals('odt', $file->extension);

		$file = new UploadedFile(__DIR__ . '/../Books/test.odt', 'test.odt', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')),
				[
					'file' => $file,
					'comment' => ''
				]
			);

		$file2 = $book->files()->orderBy('id', 'desc')->first();

		$this->assertEquals('odt', $file2->extension);

		$response->assertRedirect(route('books.files.edit', ['book' => $book, 'file' => $file2]))
			->assertSessionHasErrors(['comment' => __('validation.required', ['attribute' => __('book_file.comment')])]);
	}

	public function testZipFb2()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$file = new UploadedFile(__DIR__ . '/../Books/test.fb2.zip', 'test.fb2.zip', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', ['book' => $book]),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasNoErrors();

		$this->assertEquals('fb2', $book->files()->first()->extension);
	}

	public function testStoreFb3()
	{
		$user = User::factory()->admin()->create();

		$book = Book::factory()->create();

		$file = new UploadedFile(__DIR__ . '/../Books/test.fb3', 'test.fb3', null, null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', $book),
				['file' => $file]
			)->assertRedirect()
			->assertSessionHasErrors(['file' => __('validation.book_file_extension', ['attribute' => __('book.file')])]);
	}
}