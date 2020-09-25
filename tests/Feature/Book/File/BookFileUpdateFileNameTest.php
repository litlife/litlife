<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use Tests\TestCase;

class BookFileUpdateFileNameTest extends TestCase
{
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
}
