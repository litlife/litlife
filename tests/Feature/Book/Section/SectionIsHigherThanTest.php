<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\Section;
use Tests\TestCase;

class SectionIsHigherThanTest extends TestCase
{
	public function testIsHigherThan()
	{
		$book = Book::factory()->create();

		$section1 = Section::factory()->chapter()->create(['book_id' => $book->id]);
		$subsection1 = Section::factory()->chapter()->create(['book_id' => $book->id]);
		$section1->appendNode($subsection1);

		$section2 = Section::factory()->chapter()->create(['book_id' => $book->id]);
		$subsection2 = Section::factory()->chapter()->create(['book_id' => $book->id]);
		$section2->appendNode($subsection2);

		$this->assertTrue($section1->isHigherThan($section2));
		$this->assertTrue($subsection1->isHigherThan($section2));
		$this->assertTrue($section1->isHigherThan($subsection2));
		$this->assertTrue($subsection1->isHigherThan($subsection2));
		$this->assertTrue($section2->isHigherThan($subsection2));

		$this->assertFalse($section2->isHigherThan($section1));
		$this->assertFalse($section2->isHigherThan($subsection1));

		$this->assertFalse($subsection2->isHigherThan($section2));
		$this->assertFalse($subsection2->isHigherThan($section1));
		$this->assertFalse($subsection2->isHigherThan($subsection1));

		$this->assertFalse($section2->isHigherThan($section1));
		$this->assertFalse($section2->isHigherThan($subsection1));

		$this->assertFalse($subsection1->isHigherThan($section1));

		$this->assertFalse($section1->isHigherThan($section1));
		$this->assertFalse($subsection1->isHigherThan($subsection1));
	}
}
