<?php

namespace Tests\Feature\Book;

use App\Book;
use Tests\TestCase;

class BookGetFirstPaidSectionTest extends TestCase
{
    public function testWithOneChapter()
    {
        $book = Book::factory()->on_sale()->with_section()->create();

        $section = $book->sections()->chapter()->defaultOrder()->first();

        $this->assertTrue($section->is($book->getFirstPaidSection()));
    }

    public function testWithThreeChapters()
    {
        $book = Book::factory()->on_sale()->with_three_sections()->create(['free_sections_count' => 1]);

        $sections = $book->sections()->chapter()->defaultOrder()->get();

        $firstPaidSection = $book->getFirstPaidSection();

        $this->assertEquals(3, $sections->count());

        $this->assertNotEquals($sections->get(0)->id, $firstPaidSection->id);
        $this->assertEquals($sections->get(1)->id, $firstPaidSection->id);
        $this->assertNotEquals($sections->get(2)->id, $firstPaidSection->id);
    }
}
