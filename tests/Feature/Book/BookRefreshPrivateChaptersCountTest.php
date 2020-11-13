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

        $section = Section::factory()->private()->create(['book_id' => $book->id]);

        $section2 = Section::factory()->private()->create(['book_id' => $book->id]);

        $book->refreshPrivateChaptersCount();
        $book->save();

        $this->assertEquals(2, $book->private_chapters_count);
    }
}
