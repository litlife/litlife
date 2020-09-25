<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Section;
use Tests\TestCase;

class BookRefreshPrivateChaptersCountTest extends TestCase
{
	public function testRefreshPrivateChaptersCount()
	{
		$book = factory(Book::class)->create();

		$section = factory(Section::class)->states('private')
			->create(['book_id' => $book->id]);

		$section2 = factory(Section::class)->states('private')
			->create(['book_id' => $book->id]);

		$book->refreshPrivateChaptersCount();
		$book->save();

		$this->assertEquals(2, $book->private_chapters_count);
	}
}
