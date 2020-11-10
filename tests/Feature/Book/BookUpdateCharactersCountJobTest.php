<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Jobs\Book\BookUpdateCharactersCountJob;
use Tests\TestCase;

class BookUpdateCharactersCountJobTest extends TestCase
{
	public function testCharactersCount()
	{
		$book = Book::factory()->accepted()->with_annotation()->with_section()->with_note()->create();

		$annotation = $book->annotation;
		$annotation->characters_count = 100;
		$annotation->save();

		$section = $book->sections()->where('type', 'section')->first();
		$section->characters_count = 100;
		$section->save();

		$note = $book->sections()->where('type', 'note')->first();
		$note->characters_count = 100;
		$note->save();

		dispatch(new BookUpdateCharactersCountJob($book));

		$this->assertEquals(100, $book->fresh()->characters_count);
	}
}
