<?php

namespace Tests\Feature\Book\Keyword;

use App\Book;
use Tests\TestCase;

class BookKeywordCreatePolicyTest extends TestCase
{
	public function testCanIfBookPrivate()
	{
		$book = Book::factory()->private()->with_create_user()->create();

		$user = $book->create_user;

		$this->assertTrue($user->can('addKeywords', $book));
	}

	public function testCantIfNoPermission()
	{
		$book = Book::factory()->accepted()->with_create_user()->create();

		$user = $book->create_user;

		$this->assertFalse($user->can('addKeywords', $book));
	}

	public function testCanIfHasPermission1()
	{
		$book = Book::factory()->accepted()->with_create_user()->create();

		$user = $book->create_user;
		$user->group->book_keyword_add = true;
		$user->save();

		$this->assertTrue($user->can('addKeywords', $book));
	}

	public function testCanIfHasPermission2()
	{
		$book = Book::factory()->accepted()->with_create_user()->create();

		$user = $book->create_user;
		$user->group->book_keyword_add_new_with_check = true;
		$user->save();

		$this->assertTrue($user->can('addKeywords', $book));
	}

	public function testCanIfHasPermission3()
	{
		$book = Book::factory()->accepted()->with_create_user()->create();

		$user = $book->create_user;
		$user->group->book_keyword_moderate = true;
		$user->save();

		$this->assertTrue($user->can('addKeywords', $book));
	}
}
