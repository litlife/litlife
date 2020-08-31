<?php

namespace Tests\Feature\Artisan;

use App\BookFile;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RemoveAutoCreatedBookFilesIfOldReadFormatTest extends TestCase
{
	public function testFileDeleted()
	{
		$file = factory(BookFile::class)->states('txt')->create();
		$file->format = 'epub';
		$file->auto_created = true;
		$file->save();

		$book = $file->book;
		$book->online_read_new_format = false;
		$book->save();

		$this->assertFalse($book->isPagesNewFormat());

		Artisan::call('clear:remove_auto_created_book_files_if_old_read_format', ['min_id' => $book->id]);

		$book->refresh();
		$file->refresh();

		$this->assertTrue($file->trashed());
		$this->assertEquals(0, $book->files()->count());
		$this->assertEquals(0, $book->files_count);
	}

	public function testDontRemoveIfFileNotAutoCreated()
	{
		$file = factory(BookFile::class)->states('txt')->create();
		$file->format = 'epub';
		$file->auto_created = false;
		$file->save();

		$book = $file->book;
		$book->online_read_new_format = false;
		$book->save();

		$this->assertFalse($book->isPagesNewFormat());

		Artisan::call('clear:remove_auto_created_book_files_if_old_read_format', ['min_id' => $book->id]);

		$book->refresh();
		$file->refresh();

		$this->assertFalse($file->trashed());
		$this->assertEquals(1, $book->files()->count());
		$this->assertEquals(1, $book->files_count);
	}
}
