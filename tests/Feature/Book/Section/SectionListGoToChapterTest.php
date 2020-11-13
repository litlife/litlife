<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use Tests\TestCase;

class SectionListGoToChapterTest extends TestCase
{
    public function testIsOkNewPageFormat()
    {
        $book = Book::factory()->with_section()->create();

        $this->get(route('books.sections.list_go_to', ['book' => $book]))
            ->assertOk()
            ->assertViewHas('book', $book)
            ->assertViewIs('book.chapter.list_go_to');
    }
}