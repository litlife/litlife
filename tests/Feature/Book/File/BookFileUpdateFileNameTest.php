<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use Tests\TestCase;

class BookFileUpdateFileNameTest extends TestCase
{
	public function testUpdateFileName()
	{
		$file = BookFile::factory()->txt()->create();

		$book = Book::factory()->without_any_authors()->create();

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
		$file = BookFile::factory()->txt()->zip()->create();

		$book = Book::factory()->without_any_authors()->create();

		$file->book()->associate($book);

		$file->updateFileName();
		$file->refresh();

		$this->assertTrue($file->exists());
		$this->assertTrue($file->isZipArchive());
		$this->assertRegExp('/^Kniga_([A-z0-9]{5})\.txt.zip/iu', $file->name);
		$this->assertRegExp('/^Kniga_([A-z0-9]{5})\.txt/iu', $file->getFirstFileInArchive());
	}
}
