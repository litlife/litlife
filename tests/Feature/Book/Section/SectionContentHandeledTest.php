<?php

namespace Tests\Feature\Book\Section;

use App\Attachment;
use App\Book;
use App\Section;
use Tests\TestCase;

class SectionContentHandeledTest extends TestCase
{
	public function testContentHandeledNotesAnchors()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->create([
				'book_id' => $book->id,
				'content' => '<p><a href="#test2">текст</a> <span id="test1">текст</span></p>'
			])->fresh();

		$section2 = factory(Section::class)
			->states('note')
			->create([
				'book_id' => $book->id,
				'content' => '<p><a href="#test1">текст</a> <span id="test2">текст</span></p>'
			])->fresh();

		$this->assertEquals('<p><a data-type="note" data-section-id="2" href="http://dev.litlife.club/books/' . $book->id . '/notes/2?page=1#u-test2">текст</a> <span id="u-test1">текст</span></p>',
			$section->getContentHandeled());

		$this->assertEquals('<p><a data-type="section" data-section-id="1" href="http://dev.litlife.club/books/' . $book->id . '/sections/1?page=1#u-test1">текст</a> <span id="u-test2">текст</span></p>',
			$section2->getContentHandeled());
	}

	public function testContentHandeledImages()
	{
		$book = Book::factory()->create();

		$attachment = Attachment::factory()->create([
				'book_id' => $book->id
			])->fresh();

		$section = Section::factory()->create([
				'book_id' => $book->id,
				'content' => '<p><img src="' . $attachment->url . '"/></p>'
			])->fresh();

		$this->assertEquals('<p><img class="img-fluid"  src="' . $attachment->url . '" alt="test.jpeg"/></p>',
			$section->getContentHandeled());
	}

	public function testContentHandeledNotesAnchorsIfAnchorNotExists()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->create([
				'book_id' => $book->id,
				'content' => '<p><a href="#test2">текст</a> <span id="test1">текст</span></p>'
			])->fresh();

		$this->assertEquals('<p><a href="#u-test2">текст</a> <span id="u-test1">текст</span></p>',
			$section->getContentHandeled());
	}

	public function testContentHandeledIfRemoteLinkWithHash()
	{
		$book = Book::factory()->create();

		$section = Section::factory()->create([
				'book_id' => $book->id,
				'content' => '<p><a href="https://example.com/test/?query=value#test2">текст</a> <span id="test1">текст</span></p>'
			])->fresh();

		$this->assertEquals('<p><a href="/away?url=https%3A%2F%2Fexample.com%2Ftest%2F%3Fquery%3Dvalue%23test2">текст</a> <span id="u-test1">текст</span></p>',
			$section->getContentHandeled());
	}
}
