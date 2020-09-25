<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Section;
use Tests\TestCase;

class SectionIsPaidTest extends TestCase
{
	public function testIsPaid()
	{
		$book = factory(Book::class)
			->states('on_sale')->create(['free_sections_count' => 3]);

		$section1 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$subsection1 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$section1->appendNode($subsection1);

		$section2 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$subsection2 = factory(Section::class)->states('chapter')->create(['book_id' => $book->id]);
		$section2->appendNode($subsection2);

		$this->assertFalse($section1->isPaid());
		$this->assertFalse($subsection1->isPaid());
		$this->assertFalse($section2->isPaid());
		$this->assertTrue($subsection2->isPaid());
	}
}
