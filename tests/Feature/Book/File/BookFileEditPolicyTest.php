<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use Tests\TestCase;

class BookFileEditPolicyTest extends TestCase
{
	public function testCanIfFilePrivate()
	{
		$book = factory(Book::class)->states('with_create_user')->create();

		$user = $book->create_user;

		$file = factory(BookFile::class)
			->states('odt', 'private')
			->create(['create_user_id' => $user->id]);

		$user->group->add_book = true;
		$user->push();

		$this->assertTrue($user->can('update', $file));
	}

	public function testCanIfOnReview()
	{
		$book = factory(Book::class)->states('with_create_user')->create();

		$user = $book->create_user;

		$file = factory(BookFile::class)
			->states('odt', 'sent_for_review')
			->create(['create_user_id' => $user->id]);

		$user->group->add_book = true;
		$user->push();

		$this->assertTrue($user->can('update', $file));
	}
}
