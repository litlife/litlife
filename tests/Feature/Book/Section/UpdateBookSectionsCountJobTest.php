<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Jobs\Book\UpdateBookSectionsCount;
use Tests\TestCase;

class UpdateBookSectionsCountJobTest extends TestCase
{
    public function testUpdate()
    {
        $book = Book::factory()->with_three_sections()->create();

        $book->sections_count = 0;
        $book->save();

        $this->assertEquals(0, $book->sections_count);

        UpdateBookSectionsCount::dispatch($book);

        $book->refresh();

        $this->assertEquals(3, $book->sections_count);
    }
}
