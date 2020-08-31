<?php

namespace Tests\Feature\Book\BookGroup;

use App\Book;
use App\User;
use Tests\TestCase;

class BookGroupPolicyTest extends TestCase
{
	public function testCantGroupOrUngroupIfNoPermission()
	{
		$book = factory(Book::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('group', $book));
		$this->assertFalse($user->can('ungroup', $book));
		$this->assertFalse($user->can('make_main_in_group', $book));
	}

	public function testCantGroupOrUngroupIfBookPrivate()
	{
		$book = factory(Book::class)
			->states('private')
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertFalse($user->can('group', $book));
		$this->assertFalse($user->can('ungroup', $book));
		$this->assertFalse($user->can('make_main_in_group', $book));
	}

	public function testCantGroupOrUngroup()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertTrue($user->can('group', $book));
		$this->assertTrue($user->can('ungroup', $book));
		$this->assertTrue($user->can('make_main_in_group', $book));
	}

	public function testCantGroupOrUngroupIfBookDeleted()
	{
		$book = factory(Book::class)->states('accepted')->create();
		$book->delete();

		$user = factory(User::class)
			->states('admin')
			->create();

		$this->assertFalse($user->can('group', $book));
		$this->assertFalse($user->can('ungroup', $book));
		$this->assertFalse($user->can('make_main_in_group', $book));
	}
}
