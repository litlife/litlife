<?php

namespace Tests\Feature\Book\File;

use App\BookFile;
use App\BookFileDownloadLog;
use App\BookParse;
use App\Enums\StatusEnum;
use App\User;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookFileDeleteRestoreTest extends TestCase
{
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

	public function testCancelFailedParseIfBookFileSourceDeleted()
	{
		$admin = factory(User::class)
			->states('admin')->create();

		$parse = factory(BookParse::class)
			->states('failed')
			->create();

		$book = $parse->book;

		$file = factory(BookFile::class)
			->states('txt')
			->create(['book_id' => $book->id, 'source' => true]);

		$this->assertTrue($admin->can('delete', $file));

		$this->actingAs($admin)
			->delete(route('books.files.destroy', ['book' => $book, 'file' => $file]))
			->assertOk();

		$book->refresh();

		$this->assertTrue($book->parse->isSucceed());
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
}
