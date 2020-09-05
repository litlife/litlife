<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use App\User;
use Tests\TestCase;

class SectionViewSectionListPolicyTest extends TestCase
{
	public function testFalseIfBookNotParsedAndBookHasOnlyDescription()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_section')
			->create();
		$book->parse->start();
		$book->push();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('view_section_list', $book));
	}

	public function testTrueIfBookPrivateAndUserCreator()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$user = $book->create_user;

		$this->assertTrue($user->can('view_section_list', $book));
	}

	public function testFalseIfBookPrivateAndUserNotCreator()
	{
		$book = factory(Book::class)
			->states('private')
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('view_section_list', $book));
	}
}