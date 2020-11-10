<?php

namespace Tests\Feature\Book\Publish;

use App\Book;
use App\User;
use Tests\TestCase;

class BookPublishPolicyTest extends TestCase
{
	public function testCanIfCreatorAndBookPrivate()
	{
		$book = Book::factory()->private()->with_create_user()->create();

		$user = $book->create_user;

		$this->assertTrue($user->can('publish', $book));
	}

	public function testCanPublishOnReviewIfCanCheckBooks()
	{
		$book = Book::factory()->sent_for_review()->create();

		$user = User::factory()->create();
		$user->group->check_books = true;
		$user->push();

		$this->assertTrue($user->can('publish', $book));
	}

	public function testCantPublishOnReviewIfCantCheckBooks()
	{
		$book = Book::factory()->sent_for_review()->create();

		$user = User::factory()->create();
		$user->group->check_books = false;
		$user->push();

		$this->assertFalse($user->can('publish', $book));
	}

	public function testCantIfPublished()
	{
		$book = Book::factory()->accepted()->create();

		$user = User::factory()->create();
		$user->group->check_books = true;
		$user->push();

		$this->assertFalse($user->can('publish', $book));
	}

	public function testCanPublishPrivateIfCanCheckBooks()
	{
		$book = Book::factory()->private()->create();

		$user = User::factory()->create();
		$user->group->check_books = true;
		$user->push();

		$this->assertTrue($user->can('publish', $book));
	}

	public function testCanPublishOnReviewIfCanPublishWithoutReview()
	{
		$book = Book::factory()->sent_for_review()->with_create_user()->create();

		$user = $book->create_user;
		$user->group->add_book_without_check = true;
		$user->push();

		$this->assertTrue($user->can('publish', $book));
	}

	public function testCantIfBookNotSucceedParsedAndBookNotDescriptionOnly()
	{
		$book = Book::factory()->private()->with_create_user()->with_section()->create();
		$book->parse->wait();
		$book->push();

		$user = $book->create_user;

		$this->assertFalse($user->can('publish', $book));
	}
}
