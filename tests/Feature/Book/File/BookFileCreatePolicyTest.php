<?php

namespace Tests\Feature\Book\File;

use App\Author;
use App\Book;
use App\User;
use Tests\TestCase;

class BookFileCreatePolicyTest extends TestCase
{
	public function testCanIfBookPrivateAndUserCreator()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();
		$book->downloadAccessEnable();
		$book->save();

		$user = $book->create_user;
		$user->group->book_file_add = false;
		$user->save();

		$this->assertTrue($user->can('addFiles', $book));
	}

	public function testCantIfDoesntHavePermissions()
	{
		$user = factory(User::class)->create();
		$user->group->book_file_add = false;
		$user->save();

		$book = factory(Book::class)->states('accepted')->create();
		$book->downloadAccessEnable();
		$book->save();

		$this->assertFalse($user->can('addFiles', $book));
	}

	public function testCanIfHasPermissionBookDownloadAccessEnable()
	{
		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->save();

		$book = factory(Book::class)->states('accepted')->create();
		$book->downloadAccessEnable();
		$book->save();

		$this->assertTrue($user->can('addFiles', $book));
	}

	public function testCantIfHasPermissionAndBookDownloadAccessDisable()
	{
		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->save();

		$book = factory(Book::class)->states('accepted')->create();
		$book->downloadAccessDisable();
		$book->save();

		$this->assertFalse($user->can('addFiles', $book));
	}

	public function testCanIfHasPermissionAndHaveAccessToClosedBookAndDownloadAccessDisabled()
	{
		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->group->access_to_closed_books = true;
		$user->save();

		$book = factory(Book::class)->states('accepted')->create();
		$book->downloadAccessDisable();
		$book->save();

		$this->assertTrue($user->can('addFiles', $book));
	}

	public function testPolicy()
	{
		$user = factory(User::class)->create();

		$book = factory(Book::class)
			->states('private', 'with_file')
			->create(['create_user_id' => $user->id])
			->fresh();

		$this->assertEquals(1, $book->files_count);

		$book->parse->start();
		$book->push();
		$book->refresh();

		$this->assertFalse($book->parse->isSucceed());
		$this->assertFalse($book->isDescriptionOnly());

		$this->assertTrue($user->can('addFiles', $book));

		$book->parse->wait();
		$book->push();
		$book->refresh();

		$this->assertTrue($user->can('addFiles', $book));

		$book->parse->reset();
		$book->push();
		$book->refresh();

		$this->assertTrue($user->can('addFiles', $book));

		$book->parse->failed([]);
		$book->push();
		$book->refresh();

		$this->assertTrue($user->can('addFiles', $book));

		$book->parse->success();
		$book->push();
		$book->refresh();

		$this->assertTrue($user->can('addFiles', $book));
	}

	public function testCantIfBookIsOnSale()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->states('accepted')
			->create(['price' => 100]);

		$this->assertFalse($user->can('addFiles', $book));
	}

	public function testCanIfUserVerifiedAuthorOfBookAndBookHasNoDownloadAccess()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book')
			->create();

		$user = $author->managers()->first()->user;

		$book = $author->books()->first();
		$book->downloadAccessDisable();
		$book->readAccessDisable();
		$book->save();

		$this->assertTrue($user->can('addFiles', $book));
	}
}
