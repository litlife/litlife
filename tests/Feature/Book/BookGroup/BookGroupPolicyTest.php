<?php

namespace Tests\Feature\Book\BookGroup;

use App\Book;
use App\User;
use Tests\TestCase;

class BookGroupPolicyTest extends TestCase
{
	public function testCantGroupOrUngroupIfNoPermission()
	{
		$book = Book::factory()->create();

		$user = User::factory()->create();

		$this->assertFalse($user->can('group', $book));
		$this->assertFalse($user->can('ungroup', $book));
		$this->assertFalse($user->can('make_main_in_group', $book));
	}

	public function testCantGroupOrUngroupIfBookPrivate()
	{
		$book = Book::factory()->private()->create();

		$user = User::factory()->admin()->create();

		$this->assertFalse($user->can('group', $book));
		$this->assertFalse($user->can('ungroup', $book));
		$this->assertFalse($user->can('make_main_in_group', $book));
	}

	public function testCantGroupOrUngroup()
	{
		$book = Book::factory()->accepted()->create();

		$user = User::factory()->admin()->create();

		$this->assertTrue($user->can('group', $book));
		$this->assertTrue($user->can('ungroup', $book));
		$this->assertTrue($user->can('make_main_in_group', $book));
	}

	public function testCantGroupOrUngroupIfBookDeleted()
	{
		$book = Book::factory()->accepted()->create();
		$book->delete();

		$user = User::factory()->admin()->create();

		$this->assertFalse($user->can('group', $book));
		$this->assertFalse($user->can('ungroup', $book));
		$this->assertFalse($user->can('make_main_in_group', $book));
	}
}
