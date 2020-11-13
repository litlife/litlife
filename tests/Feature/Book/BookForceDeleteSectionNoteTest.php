<?php

namespace Tests\Feature\Book;

use App\Page;
use App\Section;
use Tests\TestCase;

class BookForceDeleteSectionNoteTest extends TestCase
{
    public function testPageDeleted()
    {
        $section = Section::factory()->with_two_pages()->create();

        $page = $section->pages()->first();
        $book = $page->book;

        $this->assertNotNull($section);
        $this->assertNotNull($book);
        $this->assertTrue($section->book->is($book));
        $this->assertEquals('section', $section->type);

        $book->forceDeleteSectionNote();

        $this->assertEquals(0, $book->sections()->chapter()->where('id', $section->id)->count());
        $this->assertEquals(0, Page::where('section_id', $section->id)->count());
        $this->assertEquals(0, Page::where('id', $page->id)->count());
    }
}