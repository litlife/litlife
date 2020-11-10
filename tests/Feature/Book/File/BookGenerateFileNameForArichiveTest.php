<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use Tests\TestCase;

class BookGenerateFileNameForArichiveTest extends TestCase
{
	public function testGenerateFileNameForArichive()
	{
		$file = factory(BookFile::class)->states('fb2')->create();

		$book = Book::factory()->without_any_authors()->create();

		$file->book()->associate($book);

		$this->assertRegExp('/^Kniga_([A-z0-9]{5})\.fb2\.zip$/iu', $file->generateFileNameForArichive());
		$this->assertNotRegExp('/^Kniga_([A-z0-9]{6})\.fb2\.zip$/iu', $file->generateFileNameForArichive());
	}
}
