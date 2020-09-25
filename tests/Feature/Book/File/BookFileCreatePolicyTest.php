<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\User;
use Tests\TestCase;

class BookFileCreatePolicyTest extends TestCase
{
	public function testIfBookDownloadAccessDisablePermissions()
	{
		$user = factory(User::class)->create();
		$user->group->book_file_add = true;
		$user->save();

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->downloadAccessEnable();
		$book->save();

		$this->assertTrue($user->can('addFiles', $book));

		$book->downloadAccessDisable();
		$book->save();
		$book->refresh();

		$this->assertFalse($user->can('addFiles', $book));
	}

	public function testIfBookDownloadAccessDisableAndAccessToClosedBooksEnablePermissions()
	{
		$user = factory(User::class)->create();
		$user->group->access_to_closed_books = true;
		$user->save();

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->downloadAccessEnable();
		$book->save();

		$this->assertFalse($user->can('addFiles', $book));

		$user->group->book_file_add = true;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->can('addFiles', $book));

		$book->downloadAccessDisable();
		$book->save();
		$book->refresh();

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

	public function testCantAttachBookFileIfBookIsOnSale()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$book = factory(Book::class)
			->states('accepted')
			->create(['price' => 100]);

		$this->assertFalse($user->can('addFiles', $book));
	}

	public function testAddFilePolicy()
	{
		$book = factory(Book::class)->states('with_writer', 'with_read_and_download_access')->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('addFiles', $book));

		$user->group->book_file_add = true;
		$user->push();
		$user->refresh();

		$this->assertTrue($user->can('addFiles', $book));
	}
}
