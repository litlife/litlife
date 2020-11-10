<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use Tests\TestCase;

class BookFileGenerateFileNameTest extends TestCase
{
	public function testGenerateFileName()
	{
		$file = BookFile::factory()->txt()->zip()->create();
		$file->format = 'fb2';
		$file->save();

		$book = Book::factory()->without_any_authors()->create();

		$file->book()->associate($book);

		$this->assertRegExp('/^Kniga_([A-z0-9]{5})\.fb2$/iu', $file->generateFileName());
		$this->assertNotRegExp('/^Kniga_([A-z0-9]{6})\.fb2$/iu', $file->generateFileName());
	}
}
