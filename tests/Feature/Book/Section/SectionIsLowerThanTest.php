<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Section;
use Tests\TestCase;

class SectionIsLowerThanTest extends TestCase
{
    public function testIsLowerThan()
    {
        $book = Book::factory()->create();

        $section1 = Section::factory()->chapter()->create(['book_id' => $book->id]);
        $subsection1 = Section::factory()->chapter()->create(['book_id' => $book->id]);
        $section1->appendNode($subsection1);

        $section2 = Section::factory()->chapter()->create(['book_id' => $book->id]);
        $subsection2 = Section::factory()->chapter()->create(['book_id' => $book->id]);
        $section2->appendNode($subsection2);

        $this->assertTrue($subsection2->isLowerThan($section2));
        $this->assertTrue($subsection2->isLowerThan($section1));
        $this->assertTrue($subsection2->isLowerThan($subsection1));

        $this->assertTrue($section2->isLowerThan($section1));
        $this->assertTrue($section2->isLowerThan($subsection1));

        $this->assertTrue($subsection1->isLowerThan($section1));

        $this->assertFalse($section1->isLowerThan($section1));
        $this->assertFalse($subsection1->isLowerThan($subsection1));

        $this->assertFalse($section1->isLowerThan($section2));
        $this->assertFalse($subsection1->isLowerThan($section2));
        $this->assertFalse($section1->isLowerThan($subsection2));
        $this->assertFalse($subsection1->isLowerThan($subsection2));
        $this->assertFalse($section2->isLowerThan($subsection2));
    }
}
