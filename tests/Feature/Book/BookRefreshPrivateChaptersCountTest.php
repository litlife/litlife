<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Section;
use Tests\TestCase;

class BookRefreshPrivateChaptersCountTest extends TestCase
{
	public function testRefreshPrivateChaptersCount()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->private()->create();

		$section2 = Section::factory()->private()->create();

		$book->refreshPrivateChaptersCount();
		$book->save();

		$this->assertEquals(2, $book->private_chapters_count);
	}
}
