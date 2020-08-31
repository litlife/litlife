<?php

namespace Tests\Feature\Book\TextProcessing;

use App\Book;
use App\BookTextProcessing;
use App\User;
use Tests\TestCase;

class BookTextProcessingPolicyTest extends TestCase
{
	public function testCreateIfUserHasCreateTextProcessingPermission()
	{
		$book = factory(Book::class)->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('createTextProcessing', $book));

		$user->group->create_text_processing_books = true;
		$user->push();

		$this->assertTrue($user->can('createTextProcessing', $book));
	}

	public function testCantCreateIfBookForbidChange()
	{
		$book = factory(Book::class)->create();
		$book->forbid_to_change = true;
		$book->save();

		$user = factory(User::class)->create();
		$user->group->create_text_processing_books = true;
		$user->push();

		$this->assertFalse($user->can('createTextProcessing', $book));
	}

	/*
		public function testCanCreateIfBookPrivateAndUserCreator()
		{
			$book = factory(Book::class)
				->states('private')
				->create();

			$user = $book->create_user;
			$user->group->create_text_processing_books = false;
			$user->push();

			$this->assertTrue($user->can('createTextProcessing', $book));
		}
	*/
	public function testCantCreateIfBookPrivateAndUserNotCreator()
	{
		$book = factory(Book::class)
			->states('private')
			->create();

		$user = factory(User::class)->create();
		$user->group->create_text_processing_books = false;
		$user->push();

		$this->assertFalse($user->can('createTextProcessing', $book));
	}

	/*
		public function testCanCreateIfUserIsVerifiedAuthor()
		{
			$author = factory(Author::class)
				->states('with_author_manager', 'with_book')
				->create();

			$manager = $author->managers->first();
			$book = $author->books->first();
			$user = $manager->user;

			$this->assertTrue($user->can('createTextProcessing', $book));
		}
	*/
	public function testCantIfPagesOldFormat()
	{
		$book = factory(Book::class)->create();
		$user = factory(User::class)->create();

		$user->group->create_text_processing_books = true;
		$user->push();

		$book->online_read_new_format = false;
		$book->save();

		$this->assertFalse($user->can('createTextProcessing', $book));

		$book->online_read_new_format = true;
		$book->save();

		$this->assertTrue($user->can('createTextProcessing', $book));
	}

	public function testCanViewTextProcessingIfUserHasCreateTextProcessingPermission()
	{
		$book = factory(Book::class)->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('viewTextProcessing', $book));

		$user->group->create_text_processing_books = true;
		$user->push();

		$this->assertTrue($user->can('viewTextProcessing', $book));
	}

	public function testCanViewTextProcessingIfTextProcessingCreated()
	{
		$processing = factory(BookTextProcessing::class)->create();

		$book = $processing->book;

		$user = $processing->create_user;

		$this->assertTrue($user->can('viewTextProcessing', $book));
	}
}
