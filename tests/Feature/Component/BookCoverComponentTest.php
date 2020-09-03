<?php

namespace Tests\Feature\Component;

use App\Book;
use App\View\Components\BookCover;
use Tests\TestCase;

class BookCoverComponentTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testNotFound()
	{
		$book = null;

		$component = new BookCover($book, 200, 200);

		$expected = <<<'blade'
<img class="{{ $class }}" alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}" data-src="{{ $url }}"/>
blade;

		$this->assertFalse($component->isShowCover());

		$this->assertEquals($expected, $component->render());

		$data = $component->data();

		$this->assertEquals(0, $data['href']);
		$this->assertEquals(null, $data['alt']);
		$this->assertStringContainsString('no_book_cover.jpeg', $data['url']);
		$this->assertEquals(200, $data['width']);
		$this->assertEquals(200, $data['height']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testBookCoverDeleted()
	{
		$book = factory(Book::class)
			->states('with_cover')
			->create();

		$book->cover->delete();

		$component = new BookCover($book, 200, 200);

		$expected = <<<'blade'
<a title="{{ $alt }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto"><img class="{{ $class }}" alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}" data-src="{{ $url }}"/></a>
blade;

		$this->assertFalse($component->isShowCover());

		$this->assertEquals($expected, $component->render());

		$data = $component->data();

		$this->assertEquals(route('books.show', ['book' => $book]), $data['href']);
		$this->assertEquals($book->title, $data['alt']);
		$this->assertStringContainsString('no_book_cover.jpeg', $data['url']);
		$this->assertEquals(200, $data['width']);
		$this->assertEquals(200, $data['height']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testBookDeleted()
	{
		$book = factory(Book::class)
			->create();

		$book->delete();

		$component = new BookCover($book, 200, 200);

		$expected = <<<'blade'
<a title="{{ $alt }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto"><img class="{{ $class }}" alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}" data-src="{{ $url }}"/></a>
blade;

		$this->assertFalse($component->isShowCover());

		$this->assertEquals($expected, $component->render());

		$data = $component->data();

		$this->assertEquals(route('books.show', ['book' => $book]), $data['href']);
		$this->assertEquals(null, $data['alt']);
		$this->assertStringContainsString('no_book_cover.jpeg', $data['url']);
		$this->assertEquals(200, $data['width']);
		$this->assertEquals(200, $data['height']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testBookCoverExists()
	{
		$book = factory(Book::class)
			->states('with_cover')
			->create();

		$component = new BookCover($book, 200, 200);

		$expected = <<<'blade'
<a title="{{ $alt }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto"><img class="{{ $class }}" alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}" data-src="{{ $url }}"/></a>
blade;

		$this->assertTrue($component->isShowCover());

		$this->assertEquals($expected, $component->render());

		$data = $component->data();

		$this->assertEquals(route('books.show', ['book' => $book]), $data['href']);
		$this->assertEquals($book->title, $data['alt']);
		$this->assertStringContainsString($book->cover->url, $data['url']);
		$this->assertEquals(200, $data['width']);
		$this->assertEquals(200, $data['height']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testShowEvenIfTrashed()
	{
		$book = factory(Book::class)
			->states('with_cover')
			->create();

		$book->delete();

		$component = new BookCover($book, 200, 200, 90, 1, '', '', 1);

		$this->assertTrue($component->isShowCover());
	}

	public function testDontShowIfDontHaveAccess()
	{
		$book = factory(Book::class)
			->states('with_cover', 'private', 'with_create_user')
			->create();

		$component = new BookCover($book, 200, 200);

		$this->assertFalse($component->isShowCover());

		$data = $component->data();

		$this->assertNull($data['alt']);
	}

	public function testShowIfHaveAccess()
	{
		$book = factory(Book::class)
			->states('with_cover', 'private', 'with_create_user')
			->create();

		$this->be($book->create_user);

		$component = new BookCover($book, 200, 200);

		$this->assertTrue($component->isShowCover());

		$data = $component->data();

		$this->assertEquals($book->title, $data['alt']);
	}
}
