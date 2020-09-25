<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use Illuminate\Support\Facades\Storage;
use Litlife\Fb2\Fb2;
use Tests\TestCase;

class BookFileGetStreamOrFirstFileInArchiveStreamTest extends TestCase
{
	public function testGetStreamOrFirstFileInArchiveStream()
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

		$stream = $file->getStreamOrFirstFileInArchiveStream();

		$fb2 = new Fb2();
		$fb2->setFile($stream);
		$this->assertIsObject($fb2->description());

		$book = factory(Book::class)
			->create();

		$file = new BookFile;
		$file->open(__DIR__ . '/../Books/test.fb2');
		$file->statusAccepted();
		$book->files()->save($file);
		$file->refresh();

		$stream = $file->getStreamOrFirstFileInArchiveStream();

		$fb2 = new Fb2();
		$fb2->setFile($stream);
		$this->assertIsObject($fb2->description());
	}
}