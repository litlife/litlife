<?php

namespace Tests\Feature\Book\File;

use App\Book;
use App\BookFile;
use App\User;
use Tests\TestCase;

class BookFilePolicyTest extends TestCase
{

	public function testCreateIfBookDownloadAccessDisablePermissions()
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

	public function testCreateIfBookDownloadAccessDisableAndAccessToClosedBooksEnablePermissions()
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

	public function testAddFilePolicyTest()
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

		$this->assertFalse($book->isParsed());
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

	public function testUserCanUpdateFileIfFilePrivate()
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

	public function testUserCanUpdateFileIfFileOnReview()
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