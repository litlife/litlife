<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use App\Section;
use Tests\TestCase;

class BookUpdatePageNumbersJobTest extends TestCase
{
    public function test()
    {
        $book = Book::factory()->create();

        $chapter1 = Section::factory()->chapter()->with_two_pages()->create(['book_id' => $book->id]);

        $chapter2 = Section::factory()->chapter()->with_two_pages()->create(['book_id' => $book->id]);

        $chapter2->appendToNode($chapter1)->save();

        $this->assertEquals(2, $book->sections()->chapter()->count());
        $this->assertEquals(2, $chapter1->pages_count);
        $this->assertEquals(2, $chapter2->pages_count);

        BookUpdatePageNumbersJob::dispatch($book);

        $this->assertEquals(1, $chapter1->pages()->get()->get(0)->book_page);
        $this->assertEquals(2, $chapter1->pages()->get()->get(1)->book_page);
        $this->assertEquals(3, $chapter2->pages()->get()->get(0)->book_page);
        $this->assertEquals(4, $chapter2->pages()->get()->get(1)->book_page);

        $book->refresh();

        $this->assertEquals(4, $book->page_count);
    }
}
