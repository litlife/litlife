<?php

namespace Tests\Feature\Component;

use App\Book;
use App\View\Components\BookName;
use Tests\TestCase;

class BookNameComponentTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testNotFound()
	{
		$book = null;

		$component = new BookName($book, true, true);

		$this->assertEquals('<span>' . __('Book is not found') . '</span>', $component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSoftDeleted()
	{
		$book = factory(Book::class)->create();
		$book->delete();

		$component = new BookName($book, true, false);

		$this->assertEquals('<span><a href="' . route('books.show', $book) . '">' . __('Book was deleted') . '</a></span>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testHrefDisable()
	{
		$book = factory(Book::class)->create();

		$component = new BookName($book, false, false, false);

		$this->assertEquals('<span>' . $book->title . '</span>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testBadgeSi()
	{
		$book = factory(Book::class)->states('si_true', 'lp_false')->create();

		$component = new BookName($book, false, true, false);

		$this->assertEquals('<span>' .
			$book->title .
			' <span class="text-muted" data-toggle="tooltip" data-placement="top" title="' . __('book.is_si') . '">(' . __('book.si') . ')</span></span>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testBadgeLp()
	{
		$book = factory(Book::class)->states('lp_true', 'si_false')->create();

		$component = new BookName($book, false, true, false);

		$this->assertEquals('<span>' .
			$book->title .
			' <span class="text-muted" data-toggle="tooltip" data-placement="top" title="' . __('book.is_lp') . '">(' . __('book.lp') . ')</span></span>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testBadgeIsCollection()
	{
		$book = factory(Book::class)->states('lp_false', 'si_false')->create();
		$book->is_collection = true;
		$book->save();

		$component = new BookName($book, false, true, false);

		$this->assertEquals('<span>' .
			$book->title .
			' <span class="text-muted text-lowercase">(' . __('book.is_collection') . ')</span></span>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testBadgeWithAge()
	{
		$book = factory(Book::class)->states('lp_false', 'si_false')->create();
		$book->age = 18;
		$book->save();

		$component = new BookName($book, false, true, false);

		$this->assertEquals('<span>' .
			$book->title .
			' <sup><span class="text-muted">' . $book->age . '+</span></sup></span>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testBadgeIsPrivate()
	{
		$book = factory(Book::class)->states('lp_false', 'si_false', 'private')->create();
		$book->age = 0;
		$book->save();

		$this->be($book->create_user);

		$component = new BookName($book, false, true, false);

		$this->assertEquals('<span>' .
			$book->title .
			' <i class="fas fa-lock" data-toggle="tooltip" data-placement="top" title="' . __('book.private_tooltip') . '"></i></span>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testNameIfDontHaveAccess()
	{
		$book = factory(Book::class)->states('private')->create();

		$component = new BookName($book, false, false, false);

		$this->assertEquals('<span>' .
			__('Access to the book is restricted') .
			'</span>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testShowEvenIfTrashed()
	{
		$book = factory(Book::class)->states('lp_false', 'si_false')->create();
		$book->delete();

		$component = new BookName($book, false, true, true);

		$this->assertEquals('<span>' . $book->title . ' <span class="text-muted">(' . __('Book was deleted') . ')</span></span>', $component->render());
	}
}
