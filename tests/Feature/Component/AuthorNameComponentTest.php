<?php

namespace Tests\Feature\Component;

use App\Author;
use App\View\Components\AuthorName;
use Tests\TestCase;

class AuthorNameComponentTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testDeleted()
	{
		$user = null;

		$component = new AuthorName($user);

		$this->assertEquals(__('Author is not found'), $component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testSoftDeleted()
	{
		$author = factory(Author::class)->create();
		$author->delete();

		$component = new AuthorName($author);

		$this->assertEquals('<a class="author name"  href="' . route('authors.show', $author) . '">' . __('Author deleted') . '</a> (' . $author->lang . ')',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testHrefEnable()
	{
		$author = factory(Author::class)->create();

		$component = new AuthorName($author);

		$this->assertEquals('<a class="author name"  href="' . route('authors.show', $author) . '">' .
			$author->last_name . ' ' . $author->first_name . ' ' . $author->middle_name . ' ' . $author->nickname
			. '</a> (' . $author->lang . ')',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testHrefDisable()
	{
		$author = factory(Author::class)->create();

		$component = new AuthorName($author, false);

		$this->assertEquals($author->last_name . ' ' . $author->first_name . ' ' . $author->middle_name . ' ' . $author->nickname . ' (' . $author->lang . ')',
			$component->render());
	}
}
