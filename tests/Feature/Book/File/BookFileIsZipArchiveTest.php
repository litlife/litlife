<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BookFileIsZipArchiveTest extends TestCase
{
	public function testIsZipArcive()
	{
		$file = __DIR__ . '/../Books/invalid.zip';

		$this->assertFileExists($file);

		$zip = new \ZipArchive();
		$res = $zip->open($file);

		$this->assertEquals(\ZipArchive::ER_INCONS, $res);

		Storage::fake(config('filesystems.default'));

		$book = Book::factory()->create();

		$file = new BookFile;
		$file->zip = true;
		$file->open(__DIR__ . '/../Books/test.epub');
		$file->statusAccepted();
		$book->files()->save($file);
		$file->refresh();

		$this->assertFalse($file->isZipArchive());

		$book = Book::factory()->create();

		$file = new BookFile;
		$file->zip = true;
		$file->open(__DIR__ . '/../Books/test.odt');
		$file->statusAccepted();
		$book->files()->save($file);
		$file->refresh();

		$this->assertFalse($file->isZipArchive());
	}
}
