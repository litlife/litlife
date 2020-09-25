<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use Illuminate\Support\Facades\Storage;
use Litlife\Fb2\Fb2;
use Tests\TestCase;

class BookFileGetFirstFileInArchiveStreamTest extends TestCase
{
	public function testGetFirstFileInArchiveStream()
	{
		Storage::fake(config('filesystems.default'));

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->zip = true;
		$file->open(__DIR__ . '/../Books/test.fb2');
		$file->statusAccepted();
		$book->files()->save($file);
		$file->refresh();

		$stream = $file->getFirstFileInArchiveStream();

		$fb2 = new Fb2();
		$fb2->setFile($stream);
		$this->assertIsObject($fb2->description());
	}
}
