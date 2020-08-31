<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\BookFile;
use App\BookFileDownloadLog;
use App\BookParse;
use App\Enums\StatusEnum;
use App\Jobs\Book\UpdateBookFilesCount;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Litlife\Fb2\Fb2;
use Litlife\Url\Url;
use Tests\TestCase;
use ZipArchive;

class BookFileTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		Storage::fake(config('filesystems.default'));
	}

	public function testUploadEmptyNameHttp()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->add_book = true;
		$user->push();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.epub'));

		$file = new UploadedFile($tmpFilePath, '%.epub',
			filesize($tmpFilePath), null, true);

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

	public function testCreateHttp()
	{
		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->push();

		$book = factory(Book::class)
			->create();

		$this->actingAs($user)
			->get(route('books.files.create', ['book' => $book]))
			->assertOk();
	}

	public function testStoreHttp()
	{
		config(['activitylog.enabled' => true]);

		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->push();

		$book = factory(Book::class)
			->create();

		$comment = $this->faker->realText(100);
		$number = rand(1, 100);

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.fb2'));

		$file = new UploadedFile($tmpFilePath, 'test.fb2',
			filesize($tmpFilePath), null, true);

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

	public function testStoreHttpDontParseWaitedIfSectionsExists()
	{
		Storage::fake(config('filesystems.default'));

		$admin = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->states('accepted', 'with_section')
			->create();

		$this->assertTrue($book->parse->isSucceed());

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.fb2'));

		$file = new UploadedFile($tmpFilePath, 'test.fb2',
			filesize($tmpFilePath), null, true);

		$this->actingAs($admin)
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

	public function testCreateUnsupportFormatError()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->push();

		$book = factory(Book::class)
			->create();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/../images/test.gif'));

		$file = new UploadedFile($tmpFilePath, 'test.gif',
			filesize($tmpFilePath), null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.book_file_extension', ['attribute' => __('book.file')])]);
	}

	public function testCreateBrokenZipError()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->push();

		$book = factory(Book::class)
			->create();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/invalid.zip'));

		$file = new UploadedFile($tmpFilePath, 'invalid.zip',
			filesize($tmpFilePath), null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.zip', ['attribute' => __('book.file')])]);
	}

	public function testCreateZipUnsupportedFormatInsideError()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->push();

		$book = factory(Book::class)
			->create();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.jpeg.zip'));

		$file = new UploadedFile($tmpFilePath, 'test.jpeg.zip',
			filesize($tmpFilePath), null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', compact('book')),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.zip_book_file', ['attribute' => __('book.file')])]);
	}

	public function testCreate()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		//$file->zip = true;
		$file->open(__DIR__ . '/Books/test.fb2');
		$file->statusAccepted();
		$book->files()->save($file);

		$file->refresh();

		$this->assertEquals(1, $book->files->count());
		$this->assertEquals('fb2', $file->format);
		$this->assertEquals($file->file_size, $file->size);
		$this->assertNotNull($file->md5);
		$this->assertTrue($file->exists());
		$this->assertTrue(is_resource($file->getStream()));
		$this->assertEquals(Url::fromString($file->name)->getExtension(), $file->format);
	}

	public function testCreateInputZip()
	{
		config(['litlife.disk_for_files' => 'private']);

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		//$file->zip = true;
		$file->open(__DIR__ . '/Books/test_95.doc.zip');
		$file->statusAccepted();
		$book->files()->save($file);

		$file->refresh();

		$this->assertEquals(1, $book->files->count());
		$this->assertEquals('private', $file->storage);
		$this->assertEquals('doc', $file->format);
		$this->assertEquals($file->file_size, $file->size);
		$this->assertNotNull($file->md5);
		$this->assertTrue($file->exists());
		$this->assertTrue(is_resource($file->getStream()));
		$this->assertEquals(Url::fromString($file->name)->getExtension(), $file->format);
	}

	public function testCreateInputFile()
	{
		$string = uniqid();

		$tmp = tmpfile();
		fwrite($tmp, $string);
		$uri = stream_get_meta_data($tmp)['uri'];

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->open($uri, 'txt');
		$file->statusAccepted();
		$book->files()->save($file);

		$file->refresh();

		$this->assertEquals('txt', $file->format);
		$this->assertEquals($file->file_size, $file->size);
		$this->assertEquals(strlen($string), $file->size);
		$this->assertEquals(md5($string), $file->md5);
		$this->assertTrue($file->exists());
		$this->assertTrue(is_resource($file->getStream()));
		$this->assertEquals(Url::fromString($file->name)->getExtension(), $file->format);
		$this->assertEquals($string, $file->getContents());
		$this->assertEquals(strlen($string), $file->getSize());
	}

	public function testCreateOutputZip()
	{
		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->zip = true;
		$file->open(__DIR__ . '/Books/test.fb2');
		$file->statusAccepted();
		$book->files()->save($file);

		$file->refresh();

		$this->assertEquals(1, $book->files->count());

		$this->assertEquals('fb2', $file->format);
		$this->assertNotEquals($file->file_size, $file->size);
		$this->assertNotNull($file->md5);
		$this->assertTrue($file->exists());
		$this->assertTrue(is_resource($file->getStream()));
		$this->assertEquals('zip', Url::fromString($file->name)->getExtension());
		$this->assertEquals('fb2', Url::fromString(Url::fromString($file->name)->getFilename())->getExtension());

		$this->assertTrue($file->isZipArchive());

		$this->assertEquals(Url::fromString($file->name)->getFilename(), $file->getFirstFileInArchive());
	}

	public function testCreateInputAndOutputZip()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->zip = true;
		$file->open(__DIR__ . '/Books/test_95.doc.zip');
		$file->statusAccepted();
		$book->files()->save($file);

		$file->refresh();

		$this->assertEquals(1, $book->files->count());

		$this->assertEquals('doc', $file->format);
		$this->assertNotEquals($file->file_size, $file->size);
		$this->assertNotNull($file->md5);
		$this->assertTrue($file->exists());
		$this->assertTrue(is_resource($file->getStream()));
		$this->assertEquals('zip', Url::fromString($file->name)->getExtension());
		$this->assertEquals('doc', Url::fromString(Url::fromString($file->name)->getFilename())->getExtension());

		$this->assertTrue($file->isZipArchive());

		$this->assertEquals(Url::fromString($file->name)->getFilename(), $file->getFirstFileInArchive());
	}

	public function testGetFirstFileInArchiveStream()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->zip = true;
		$file->open(__DIR__ . '/Books/test.fb2');
		$file->statusAccepted();
		$book->files()->save($file);
		$file->refresh();

		$stream = $file->getFirstFileInArchiveStream();

		$fb2 = new Fb2();
		$fb2->setFile($stream);
		$this->assertIsObject($fb2->description());
	}

	public function testGetStreamOrFirstFileInArchiveStream()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->zip = true;
		$file->open(__DIR__ . '/Books/test.fb2');
		$file->statusAccepted();
		$book->files()->save($file);
		$file->refresh();

		$stream = $file->getStreamOrFirstFileInArchiveStream();

		$fb2 = new Fb2();
		$fb2->setFile($stream);
		$this->assertIsObject($fb2->description());

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->open(__DIR__ . '/Books/test.fb2');
		$file->statusAccepted();
		$book->files()->save($file);
		$file->refresh();

		$stream = $file->getStreamOrFirstFileInArchiveStream();

		$fb2 = new Fb2();
		$fb2->setFile($stream);
		$this->assertIsObject($fb2->description());
	}


	public function testIsZipArcive()
	{
		$file = __DIR__ . '/Books/invalid.zip';

		$this->assertFileExists($file);

		$zip = new ZipArchive();
		$res = $zip->open($file);

		$this->assertEquals(ZipArchive::ER_INCONS, $res);

		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->zip = true;
		$file->open(__DIR__ . '/Books/test.epub');
		$file->statusAccepted();
		$book->files()->save($file);
		$file->refresh();

		$this->assertFalse($file->isZipArchive());

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->zip = true;
		$file->open(__DIR__ . '/Books/test.odt');
		$file->statusAccepted();
		$book->files()->save($file);
		$file->refresh();

		$this->assertFalse($file->isZipArchive());
	}

	public function testNotFoundException()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->zip = true;

		try {
			$file->open(__DIR__ . '/Books/' . uniqid());
			$this->assertFalse(true);

		} catch (Exception $exception) {
			$this->assertEquals($exception->getMessage(), 'File or resource not found');
		}
	}

	public function testSetAsSourceAndMakeNewPages()
	{
		config(['activitylog.enabled' => true]);

		Storage::fake(config('filesystems.default'));
		Notification::fake();
		Notification::assertNothingSent();

		$user = factory(User::class)->create();
		$user->group->edit_self_book = true;
		$user->push();

		$title = $this->faker->realText(100);

		$book = factory(Book::class)
			->create([
				'title' => $title,
				'create_user_id' => $user,
				'online_read_new_format' => false
			])
			->fresh();
		$book->statusAccepted();
		$book->save();
		$this->assertFalse($book->isPagesNewFormat());

		$file = new BookFile;
		$file->open(__DIR__ . '/Books/test.fb2');
		$file->statusAccepted();
		$book->files()->save($file);

		$this->actingAs($user)
			->get(route('book_files.set_source_and_make_pages', ['file' => $file]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->parse->isWait());
		$this->assertEquals($user->id, $book->parse->create_user->id);

		$this->assertEquals(1, $file->activities()->count());
		$activity = $file->activities()->first();
		$this->assertEquals('set_as_source', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);

		Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);
		/*
				Notification::assertSentTo(
					[$user], BookFinishParseNotification::class
				);
				*/

		Notification::fake();

		$file->refresh();
		$book->refresh();

		$this->assertTrue($file->isSource());
		$this->assertTrue($book->parse->isSucceed());
		$this->assertTrue($book->isPagesNewFormat());

		$book->title = uniqid();
		$book->save();

		$title = $book->title;


		$content = $book->sections()->where('type', 'section')->first()->getContent();

		$this->assertEquals(11, $book->sections->count());
		$this->assertEquals(5, $book->page_count);
		$this->assertTrue($book->isPagesNewFormat());

		//
		$book->online_read_new_format = false;
		$book->save();
		$book->refresh();
		$this->assertFalse($book->isPagesNewFormat());

		$file2 = new BookFile;
		$file2->open(__DIR__ . '/Books/test.epub');
		$file2->statusAccepted();
		$book->files()->save($file2);
		$file2->refresh();

		$this->actingAs($user)
			->get(route('book_files.set_source_and_make_pages', ['file' => $file2]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertTrue($book->parse->isWait());
		$this->assertEquals($user->id, $book->parse->create_user->id);

		Notification::assertNothingSent();

		Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);
		/*
				Notification::assertSentTo(
					[$user], BookFinishParseNotification::class
				);
		*/
		$file->refresh();
		$file2->refresh();
		$book->refresh();

		$this->assertTrue($book->parse->isSucceed());
		$this->assertFalse($file->isSource());
		$this->assertTrue($file2->isSource());

		$content2 = $book->sections()->where('type', 'section')->first()->getContent();

		$this->assertEquals($book->title, $title);
		$this->assertGreaterThanOrEqual(1, $book->sections->count());
		$this->assertNotEquals($content, $content2);
		$this->assertEquals(2, $book->page_count);
		$this->assertTrue($book->isPagesNewFormat());
	}

	public function testViewSendForReviewHttp()
	{
		BookFile::where('status', StatusEnum::OnReview)
			->delete();

		$admin = factory(User::class)->states('with_user_group')->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$user = factory(User::class)->create();

		$file = factory(BookFile::class)->states('txt')->create();
		$file->statusSentForReview();
		$file->save();

		$this->assertTrue($file->book->isAccepted());

		$this->actingAs($admin)
			->get(route('book_files.on_moderation'))
			->assertOk()
			->assertSessionHasNoErrors()
			->assertSee($file->extension);

		$this->actingAs($user)
			->get(route('books.show', $file->book))
			->assertDontSeeText($file->extension);
	}

	public function testAcceptHttp()
	{
		config(['activitylog.enabled' => true]);

		BookFile::sentOnReview()
			->update(['status' => StatusEnum::Accepted]);

		BookFile::flushCachedOnModerationCount();

		$admin = factory(User::class)->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$file = factory(BookFile::class)->states('txt')->create();
		$file->statusSentForReview();
		$file->save();

		BookFile::flushCachedOnModerationCount();
		$this->assertEquals(1, BookFile::getCachedOnModerationCount());

		$this->followingRedirects()
			->actingAs($admin)
			->get(route('book_files.approve', ['file' => $file]))
			->assertOk()
			->assertSeeText(__('book_file.approved'))
			->assertDontSeeText($file->book->title)
			->assertDontSeeText($file->extension);

		$this->assertEquals(0, BookFile::getCachedOnModerationCount());

		$file->refresh();

		$this->assertTrue($file->isAccepted());

		$this->get(route('books.show', $file->book))
			->assertSeeText($file->extension);

		$this->assertEquals(1, $file->activities()->count());
		$activity = $file->activities()->first();
		$this->assertEquals('approved', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testDeclineHttp()
	{
		config(['activitylog.enabled' => true]);

		BookFile::sentOnReview()
			->update(['status' => StatusEnum::Accepted]);

		$admin = factory(User::class)->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$file = factory(BookFile::class)->states('txt')->create();
		$file->statusSentForReview();
		$file->save();

		BookFile::flushCachedOnModerationCount();
		$this->assertEquals(1, BookFile::getCachedOnModerationCount());

		$this->followingRedirects()
			->actingAs($admin)
			->get(route('book_files.decline', ['file' => $file]))
			->assertOk()
			->assertDontSeeText($file->extension)
			->assertSeeText(__('book_file.declined'));

		$this->assertEquals(0, BookFile::getCachedOnModerationCount());

		$file->refresh();

		$this->assertTrue($file->isRejected());

		$this->get(route('books.show', $file->book))
			->assertDontSeeText($file->extension);

		$this->assertEquals(1, $file->activities()->count());
		$activity = $file->activities()->first();
		$this->assertEquals('declined', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testDeletedHttp()
	{
		config(['activitylog.enabled' => true]);

		BookFile::sentOnReview()
			->update(['status' => StatusEnum::Accepted]);

		$admin = factory(User::class)->create();
		$admin->group->book_file_delete = true;
		$admin->push();

		$file = factory(BookFile::class)->states('txt')->create();
		$file->statusSentForReview();
		$file->save();

		BookFile::flushCachedOnModerationCount();
		$this->assertEquals(1, BookFile::getCachedOnModerationCount());

		$this->followingRedirects()
			->actingAs($admin)
			->delete(route('books.files.destroy', ['book' => $file->book, 'file' => $file]))
			->assertOk();

		$this->assertEquals(0, BookFile::getCachedOnModerationCount());

		$file->refresh();

		$this->assertTrue($file->trashed());

		$this->get(route('books.show', $file->book))
			->assertDontSeeText($file->extension);

		$this->assertEquals(1, $file->activities()->count());
		$activity = $file->activities()->first();
		$this->assertEquals('deleted', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testRestoreHttp()
	{
		config(['activitylog.enabled' => true]);

		BookFile::sentOnReview()
			->update(['status' => StatusEnum::Accepted]);

		$admin = factory(User::class)
			->states('admin')
			->create();

		$file = factory(BookFile::class)->states('txt')->create();
		$file->statusSentForReview();
		$file->save();
		$file->delete();

		$this->assertSoftDeleted($file);

		$this->actingAs($admin)
			->followingRedirects()
			->delete(route('books.files.destroy', ['book' => $file->book, 'file' => $file]))
			->assertOk();

		$file->refresh();

		$this->assertFalse($file->trashed());

		$this->assertEquals(1, BookFile::getCachedOnModerationCount());

		$this->get(route('books.show', $file->book))
			->assertSeeText($file->extension);

		$this->assertEquals(1, $file->activities()->count());
		$activity = $file->activities()->first();
		$this->assertEquals('restored', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testDownloadIfOnReview()
	{
		config(['litlife.disk_for_files' => 'public']);

		$book = factory(Book::class)->create();
		$book->statusSentForReview();
		$book->save();
		$book->refresh();

		$book_file = factory(BookFile::class)->states('txt')->create(['book_id' => $book->id]);
		$book_file->statusSentForReview();
		$book_file->save();
		UpdateBookFilesCount::dispatch($book);
		$book->refresh();

		$this->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
			->assertRedirect($book_file->url);

		$admin = factory(User::class)->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$user = factory(User::class)->create();

		$this->actingAs($admin)
			->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
			->assertRedirect($book_file->url);

		$this->actingAs($user)
			->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
			->assertRedirect($book_file->url);
	}

	public function testDownloadIfPrivate()
	{
		config(['litlife.disk_for_files' => 'public']);

		$book = factory(Book::class)->create();
		$book->statusPrivate();
		$book->save();
		$book->refresh();

		$book_file = factory(BookFile::class)->states('txt')->create([
			'book_id' => $book->id, 'create_user_id' => $book->create_user_id]);
		$book_file->statusPrivate();
		$book_file->save();
		UpdateBookFilesCount::dispatch($book);
		$book->refresh();

		$this->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
			->assertStatus(404);

		$admin = factory(User::class)->create();
		$admin->group->book_file_add_check = true;
		$admin->push();

		$user = factory(User::class)->create();

		$this->actingAs($admin)
			->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
			->assertStatus(404);

		$this->actingAs($user)
			->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
			->assertStatus(404);

		$this->actingAs($book->create_user)
			->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
			->assertRedirect($book_file->url);
	}

	public function testUploadToPrivateBook()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->push();

		$book = factory(Book::class)->create(['create_user_id' => $user->id]);
		$book->statusPrivate();
		$book->save();
		$book->refresh();

		$this->assertTrue($book->isPrivate());

		$comment = $this->faker->realText(100);
		$number = rand(1, 100);

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.fb2'));

		$file = new UploadedFile($tmpFilePath, 'test.fb2',
			filesize($tmpFilePath), null, true);

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

	public function testUploadToAcceptedBookIfCanAddWithoutCheck()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->group->book_file_add_without_check = true;
		$user->push();

		$book = factory(Book::class)->create(['create_user_id' => $user->id]);
		$book->statusAccepted();
		$book->save();
		$book->refresh();

		$this->assertTrue($book->isAccepted());

		$comment = $this->faker->realText(100);
		$number = rand(1, 100);

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.fb2'));

		$file = new UploadedFile($tmpFilePath, 'test.fb2',
			filesize($tmpFilePath), null, true);

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

	public function testDeleteIfDeclined()
	{
		$admin = factory(User::class)->create();
		$admin->group->book_file_delete = true;
		$admin->push();

		$book_file = factory(BookFile::class)->states('txt')->create();
		$book_file->statusReject();
		$book_file->save();
		$book_file->refresh();

		$response = $this->actingAs($admin)
			->delete(route('books.files.destroy', ['book' => $book_file->book, 'file' => $book_file->id]))
			->assertSessionHasNoErrors()
			->assertOk();

		$this->assertTrue($book_file->fresh()->trashed());
	}

	public function testParseDoneDatabaseNotificationDisable()
	{
		Notification::fake();
		Notification::assertNothingSent();
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->add_book = true;
		$user->email_notification_setting->db_book_finish_parse = false;
		$user->push();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.fb2'));

		$file = new UploadedFile($tmpFilePath, 'test.fb2',
			filesize($tmpFilePath), null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect()
			->assertSessionHasNoErrors();

		$book = $user->created_books()->first();

		$this->assertTrue($book->parse->isWait());
		$this->assertEquals($user->id, $book->parse->create_user->id);

		Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);
		/*
				Notification::assertSentTo(
					$user,
					BookFinishParseNotification::class,
					function ($notification, $channels) use ($book) {
						$this->assertNotContains('database', $channels);
						return $notification->book_parse->book->id === $book->id;
					}
				);
				*/
	}

	public function testParseDoneUnreadDatabaseNotificationCount()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->add_book = true;
		$user->push();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.fb2'));

		$file = new UploadedFile($tmpFilePath, 'test.fb2',
			filesize($tmpFilePath), null, true);

		$response = $this->actingAs($user)
			->post(route('books.store'),
				['file' => $file]
			)->assertRedirect()
			->assertSessionHasNoErrors();

		$book = $user->created_books()->first();

		$this->assertTrue($book->parse->isWait());
		$this->assertEquals($user->id, $book->parse->create_user->id);

		Artisan::call('book:fill_db_from_source', ['book_id' => $book->id]);
	}

	public function testCreateNewBookFileIfBookAccepted()
	{
		$book = factory(Book::class)
			->states('with_three_sections', 'accepted')
			->create();

		$book->needCreateNewBookFiles();
		$book->save();

		Artisan::call('bookfiles:make', ['bookId' => $book->id]);

		$file = $book->files()->first();

		$this->assertTrue($file->isAccepted());
	}

	public function testCreateNewBookFileIfBookPrivate()
	{
		$book = factory(Book::class)
			->states('with_three_sections', 'private')
			->create();

		$book->needCreateNewBookFiles();
		$book->save();

		Artisan::call('bookfiles:make', ['bookId' => $book->id]);

		$file = $book->files()->first();

		$this->assertTrue($file->isPrivate());
	}

	public function testCreateNewBookFileIfBookSentForReview()
	{
		$book = factory(Book::class)
			->states('with_three_sections', 'sent_for_review')
			->create();

		$book->needCreateNewBookFiles();
		$book->save();

		Artisan::call('bookfiles:make', ['bookId' => $book->id]);

		$file = $book->files()->first();

		$this->assertTrue($file->isAccepted());
	}

	public function testFactoryTxt()
	{
		$file = factory(BookFile::class)
			->states('txt')
			->create();

		$this->assertTrue($file->exists());
		$this->assertEquals('txt', $file->format);
	}

	public function testOpenFileAsStream()
	{
		$string = 'text text text';

		$book = factory(Book::class)
			->create();

		$stream = tmpfile();
		fwrite($stream, $string);

		$this->assertEquals($string, stream_get_contents($stream, -1, 0));

		$file = new BookFile;
		$file->open($stream, 'txt');

		$this->assertTrue(is_resource($file->stream));

		$book->files()->save($file);
		$file->refresh();

		$this->assertEquals(md5($string), $file->md5);
		$this->assertEquals(strlen($string), $file->size);
	}

	public function testOpenFileAsStreamSaveAsZip()
	{
		$string = 'text text text text text';

		$book = factory(Book::class)
			->states('without_any_authors')
			->create(['title' => 'Книга']);

		$stream = tmpfile();
		fwrite($stream, $string);

		$this->assertEquals($string, stream_get_contents($stream, -1, 0));

		$file = new BookFile;
		$file->zip = true;
		$file->open($stream, 'txt');

		$this->assertTrue(is_resource($file->stream));

		$book->files()->save($file);
		$file->refresh();

		$this->assertEquals(md5($string), $file->md5);
		$this->assertEquals(strlen($string), $file->file_size);
		$this->assertGreaterThan(0, $file->size);
		$this->assertTrue($file->isZipArchive());

		$this->assertRegExp('/^' . fileNameFormat($book->getNameForBookFile()) . '_([A-z0-9]{5})\.txt/iu', $file->getFirstFileInArchive());
		$this->assertRegExp('/^' . fileNameFormat($book->getNameForBookFile()) . '_([A-z0-9]{5})\.txt\.zip/iu', $file->getFirstFileInArchive() . '.zip');
	}

	public function testNameForBookFile()
	{
		$book = factory(Book::class)
			->states('without_any_authors')
			->create(['title' => 'Название книги']);

		$this->assertEquals('Название книги', $book->getNameForBookFile());

		$author = factory(Author::class)
			->create([
				'first_name' => 'Имя',
				'last_name' => 'Фамилия',
				'middle_name' => 'Отчество',
				'nickname' => 'Ник'
			]);

		$book->writers()->sync([$author->id]);
		$book->translators()->sync([$author->id]);
		$book->refresh();

		$this->assertEquals('Фамилия Имя Ник Название книги', $book->getNameForBookFile());

		$book->redaction = 5;
		$book->save();
		$book->refresh();

		$this->assertEquals('Фамилия Имя Ник Название книги r5', $book->getNameForBookFile());
	}

	public function testUpdateFileName()
	{
		$file = factory(BookFile::class)
			->states('txt')
			->create();

		$book = factory(Book::class)
			->states('without_any_authors')
			->create(['title' => 'Книга']);

		$file->book()->associate($book);

		$this->assertTrue($file->exists());

		$file->updateFileName();
		$file->refresh();

		$this->assertTrue($file->exists());
		$this->assertFalse($file->isZipArchive());
		$this->assertRegExp('/^Kniga_([A-z0-9]{5})\.txt/iu', $file->name);
	}

	public function testUpdateFileNameInZipArchive()
	{
		$file = factory(BookFile::class)
			->states('txt', 'zip')
			->create();

		$book = factory(Book::class)
			->states('without_any_authors')
			->create(['title' => 'Книга']);

		$file->book()->associate($book);

		$file->updateFileName();
		$file->refresh();

		$this->assertTrue($file->exists());
		$this->assertTrue($file->isZipArchive());
		$this->assertRegExp('/^Kniga_([A-z0-9]{5})\.txt.zip/iu', $file->name);
		$this->assertRegExp('/^Kniga_([A-z0-9]{5})\.txt/iu', $file->getFirstFileInArchive());
	}

	public function testGenerateDirname()
	{
		$file = factory(BookFile::class)
			->states('txt')
			->create();

		$this->assertEquals(getPath($file->book->id) . '/' . $file->folder, $file->generateDirName());
	}

	public function testGenerateFileName()
	{
		$file = factory(BookFile::class)
			->states('txt', 'zip')
			->create();
		$file->format = 'fb2';
		$file->save();

		$book = factory(Book::class)
			->states('without_any_authors')
			->create(['title' => 'Книга']);

		$file->book()->associate($book);

		$this->assertRegExp('/^Kniga_([A-z0-9]{5})\.fb2$/iu', $file->generateFileName());
		$this->assertNotRegExp('/^Kniga_([A-z0-9]{6})\.fb2$/iu', $file->generateFileName());
	}

	public function testGenerateFileNameForFileInsideArichive()
	{
		$file = factory(BookFile::class)
			->states('txt', 'zip')
			->create();
		$file->format = 'fb2';
		$file->save();

		$book = factory(Book::class)
			->states('without_any_authors')
			->create(['title' => 'Книга']);

		$file->book()->associate($book);

		$this->assertEquals('Kniga.fb2', $file->generateFileNameForFileInsideArichive());
	}

	public function testGenerateFileNameForArichive()
	{
		$file = factory(BookFile::class)->states('fb2')->create();

		$book = factory(Book::class)
			->states('without_any_authors')
			->create(['title' => 'Книга']);

		$file->book()->associate($book);

		$this->assertRegExp('/^Kniga_([A-z0-9]{5})\.fb2\.zip$/iu', $file->generateFileNameForArichive());
		$this->assertNotRegExp('/^Kniga_([A-z0-9]{6})\.fb2\.zip$/iu', $file->generateFileNameForArichive());
	}

	public function testIsFileShouldBeArchived()
	{
		config(['litlife.not_zip_extensions' => ['zip', 'epub', 'djvu', 'mp3', 'ogg', 'odt', 'docx']]);

		$file = new BookFile();
		$file->zip = true;
		$file->format = 'ogg';

		$this->assertFalse($file->isShouldBeArchived());

		$file->format = 'fb2';

		$this->assertTrue($file->isShouldBeArchived());

		$file->zip = false;

		$this->assertFalse($file->isShouldBeArchived());

		$file->format = 'mp3';

		$this->assertFalse($file->isShouldBeArchived());
	}

	public function testXAccelRedirect()
	{
		$file = factory(BookFile::class)
			->states('txt')
			->create(['storage' => 'private']);

		$user = factory(User::class)->create();

		$url = Storage::disk($file['storage'])
			->url($file->dirname . '/' . rawurlencode($file->name));

		$response = $this->actingAs($user)
			->get(route('books.files.show', ['book' => $file->book, 'fileName' => $file->name]))
			->assertOk()
			->assertHeader('Content-Type', 'application/x-force-download')
			->assertHeader('Content-Disposition', 'attachment; filename="' . $file->name . '"')
			->assertHeader('X-Accel-Redirect', $url);
	}

	public function testDiskForFiles()
	{
		$disk = 'private';

		config(['litlife.disk_for_files' => $disk]);

		Storage::fake($disk);

		$file = factory(BookFile::class)
			->states('txt')
			->create();

		$this->assertEquals($disk, $file->storage);

		$disk = 'public';

		config(['litlife.disk_for_files' => $disk]);

		Storage::fake($disk);

		$file = factory(BookFile::class)
			->states('txt')
			->create();

		$this->assertEquals($disk, $file->storage);

		$file = factory(BookFile::class)
			->states('txt')
			->create(['storage' => 'private']);

		$this->assertEquals('private', $file->storage);
	}

	public function testDownloadNameUrlEncode()
	{
		config(['litlife.disk_for_files' => 'public']);

		$book = factory(Book::class)
			->states('accepted')
			->create(['title' => 'Сделаешь']);

		$book_file = factory(BookFile::class)
			->states('txt')
			->create([
				'book_id' => $book->id,
				'create_user_id' => $book->create_user_id
			]);
		$book_file->save();
		UpdateBookFilesCount::dispatch($book);
		$book->refresh();

		$this->actingAs($book->create_user)
			->get(route('books.files.show', ['book' => $book, 'fileName' => $book_file->name]))
			->assertRedirect($book_file->url);
	}

	public function testStoreEmptyHttp()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->push();

		$book = factory(Book::class)
			->create();

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

	public function testUpdateHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->states('admin')->create();

		$file = factory(BookFile::class)->states('txt')->create();

		$comment = $this->faker->realText(200);
		$number = rand(1, 100);

		$this->actingAs($admin)
			->followingRedirects()
			->patch(route('books.files.update', ['book' => $file->book, 'file' => $file->id]), [
				'comment' => $comment,
				'number' => $number
			])
			->assertOk()
			->assertSeeText(__('common.data_saved'));

		$file->refresh();

		$this->assertEquals($comment, $file->comment);
		$this->assertEquals($number, $file->number);

		$this->assertEquals(1, $file->activities()->count());
		$activity = $file->activities()->first();
		$this->assertEquals('updated', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testUploadMinSize()
	{
		$admin = factory(User::class)->states('admin')->create();

		$book = factory(Book::class)->create();

		$tmp = tmpfile();
		$file = new UploadedFile(stream_get_meta_data($tmp)['uri'], 'file.epub', null, null, true);

		$response = $this->actingAs($admin)
			->post(route('books.files.store', ['book' => $book]), ['file' => $file])
			->assertSessionHasErrors(['file' => __('validation.min.file', ['attribute' => __('book_file.file'), 'min' => 1])]);
	}

	public function testSendToParsingIfAnotherOneAlreadyExists()
	{
		$admin = factory(User::class)->states('admin')->create();

		$book_parse = factory(BookParse::class)->states('successed')->create();

		$book = $book_parse->book;

		$file = factory(BookFile::class)
			->states('fb2')
			->create(['book_id' => $book->id]);

		Carbon::setTestNow(now()->addMinute());

		$this->actingAs($admin)
			->get(route('book_files.set_source_and_make_pages', ['file' => $file]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$file->refresh();
		$book->refresh();
		$latest_book_parse = $book->parse;

		$this->assertEquals(2, $book->parses()->count());

		$this->assertEquals($admin->id, $latest_book_parse->create_user->id);
		$this->assertTrue($latest_book_parse->isWait());
		$this->assertTrue($latest_book_parse->isParseOnlyPages());
		$this->assertTrue($file->isSource());
		$this->assertFalse($book->isWaitedCreateNewBookFiles());
	}

	public function testPurgeDownloadLogs()
	{
		$log = factory(BookFileDownloadLog::class)
			->create();

		$book_file = $log->book_file;

		$this->assertNotNull($book_file);
		$this->assertEquals(1, $book_file->download_logs()->count());

		$book_file->purgeDownloadLogs();

		$this->assertEquals(0, $book_file->download_logs()->count());
	}

	public function testForceDelete()
	{
		$file = factory(BookFile::class)
			->states('txt', 'with_download_log')
			->create();

		$log = $file->download_logs()->first();

		$this->assertEquals(1, $file->download_count);

		$this->assertNotNull(BookFileDownloadLog::find($log->id));
		$this->assertTrue(Storage::disk($file->storage)->exists($file->dirname . '/' . $file->name));

		$file->forceDelete();

		$this->assertFalse(Storage::disk($file->storage)->exists($file->dirname . '/' . $file->name));
		$this->assertNull(BookFileDownloadLog::find($log->id));
	}

	public function testCanParsed()
	{
		config(['litlife.book_allowed_file_extensions' => ['odt', 'docx']]);
		config(['litlife.no_need_convert' => ['mp3', 'djvu']]);

		$file = factory(BookFile::class)
			->states('odt')
			->create();

		$this->assertEquals('odt', $file->format);
		$this->assertTrue($file->canParsed());

		config(['litlife.book_allowed_file_extensions' => ['odt', 'docx']]);
		config(['litlife.no_need_convert' => ['mp3', 'djvu', 'odt']]);

		$this->assertFalse($file->canParsed());

		config(['litlife.book_allowed_file_extensions' => ['docx']]);
		config(['litlife.no_need_convert' => ['mp3', 'djvu']]);

		$this->assertFalse($file->canParsed());
	}

	public function testSentParsePagesFalseCantParsed()
	{
		config(['litlife.book_allowed_file_extensions' => ['fb2']]);

		$file = factory(BookFile::class)
			->states('fb2')
			->create();

		DB::table('book_files')
			->where('id', $file->id)
			->update(['format' => 'odt']);

		$file->refresh();

		$this->assertFalse($file->sentParsePages());
	}

	public function testSentParsePagesFalseParseWaited()
	{
		config(['litlife.book_allowed_file_extensions' => ['odt']]);

		$file = factory(BookFile::class)
			->states('odt')
			->create();

		$file->book->parse->wait();
		$file->push();
		$file->refresh();

		$this->assertFalse($file->sentParsePages());
	}

	public function testSentParsePagesFalseParseFailed()
	{
		config(['litlife.book_allowed_file_extensions' => ['odt']]);

		$file = factory(BookFile::class)
			->states('odt')
			->create();

		$file->book->parse->failed([]);
		$file->push();
		$file->refresh();

		$this->assertTrue($file->sentParsePages());
	}

	public function testSentParsePagesFalseParseStarted()
	{
		config(['litlife.book_allowed_file_extensions' => ['odt']]);

		$file = factory(BookFile::class)
			->states('odt')
			->create();

		$file->book->parse->start();
		$file->push();
		$file->refresh();

		$this->assertFalse($file->sentParsePages());
	}

	public function testSentParsePages()
	{
		config(['litlife.book_allowed_file_extensions' => ['odt']]);

		$file = factory(BookFile::class)
			->states('odt')
			->create();

		$book = $file->book;
		$book->parse->reset();
		$book->push();

		$this->assertTrue($file->sentParsePages());

		$this->assertEquals(2, $book->parses()->count());

		$file->refresh();
		$book->refresh();

		$this->assertTrue($book->is($book->parse->book));
		$this->assertTrue($file->isSource());
		$this->assertTrue($book->parse->isWait());
		$this->assertTrue($book->parse->isParseOnlyPages());
	}

	public function testUploadInvalidZipCRCError()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->create();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/invalid_crc.zip'));

		$file = new UploadedFile($tmpFilePath, 'invalid_crc.zip',
			filesize($tmpFilePath), null, true);

		$response = $this->actingAs($user)
			->post(route('books.store', compact('book')),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasErrors(['file' => __('validation.zip_book_file', ['attribute' => __('book.file')])]);
	}

	public function testAutoCreateAttribute()
	{
		$file = factory(BookFile::class)->states('txt')->create();
		$file->auto_created = false;
		$file->save();

		$this->assertFalse($file->isAutoCreated());

		$file->auto_created = true;
		$file->save();

		$this->assertTrue($file->isAutoCreated());
	}

	public function testCommentIsRequiredIfOtherFileWithSameExtensionExists()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->create();

		$file = factory(BookFile::class)
			->states('txt')
			->create(['book_id' => $book->id]);

		$file2 = factory(BookFile::class)
			->states('txt')
			->create(['book_id' => $book->id]);

		$this->actingAs($user)
			->patch(route('books.files.update', compact('book', 'file')),
				[
					'comment' => ''
				]
			)
			->assertSessionHasErrors(['comment' => __('validation.required', ['attribute' => __('book_file.comment')])])
			->assertRedirect();
	}

	public function testCommentIsNotRequiredIfOtherFileWithDifferentExtensionExists()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->create();

		$file = factory(BookFile::class)
			->states('odt')
			->create(['book_id' => $book->id]);

		$file2 = factory(BookFile::class)
			->states('txt')
			->create(['book_id' => $book->id]);

		$this->actingAs($user)
			->patch(route('books.files.update', compact('book', 'file')),
				[
					'comment' => ''
				]
			)
			->assertSessionHasNoErrors()
			->assertRedirect();
	}

	public function testStoreFileCommentRequireIfOtherFileWithSameExtensionExists()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->create();

		$file = factory(BookFile::class)
			->states('odt')
			->create(['book_id' => $book->id]);

		$this->assertEquals('odt', $file->extension);

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.odt'));

		$file = new UploadedFile($tmpFilePath, 'test.odt',
			filesize($tmpFilePath), null, true);

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

	public function testUploadDocx()
	{
		$user = factory(User::class)->states('admin')->create();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test2.docx'));

		$file = new UploadedFile($tmpFilePath, 'test2.docx',
			filesize($tmpFilePath), null, true);

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

	public function testCreateZipFb2()
	{
		Storage::fake(config('filesystems.default'));

		$user = factory(User::class)
			->states('admin')->create();

		$book = factory(Book::class)
			->create();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.fb2.zip'));

		$file = new UploadedFile($tmpFilePath, 'test.fb2.zip', filesize($tmpFilePath), null, true);

		$response = $this->actingAs($user)
			->post(route('books.files.store', ['book' => $book]),
				['file' => $file]
			)->assertRedirect();

		$response->assertSessionHasNoErrors();

		$this->assertEquals('fb2', $book->files()->first()->extension);
	}

	public function testStoreFb3()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->create();

		$admin = factory(User::class)->states('admin')->create();

		$tmpFilePath = tmpfilePath(file_get_contents(__DIR__ . '/Books/test.fb3'));

		$file = new UploadedFile($tmpFilePath, 'test.fb3', filesize($tmpFilePath), null, true);

		$response = $this->actingAs($admin)
			->post(route('books.files.store', $book),
				['file' => $file]
			)->assertRedirect()
			->assertSessionHasErrors(['file' => __('validation.book_file_extension', ['attribute' => __('book.file')])]);
	}
}