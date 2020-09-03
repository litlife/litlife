<?php

namespace Tests\Feature\Book;

use App\Book;
use App\BookFile;
use App\Jobs\Book\UpdateBookFilesCount;
use Tests\TestCase;

class BookFormatsHelperTest extends TestCase
{
	public function test1()
	{
		$book = factory(Book::class)
			->states('accepted', 'with_create_user')
			->create();

		$file = factory(BookFile::class)
			->states('odt', 'accepted')
			->create([
				'book_id' => $book->id,
				'format' => 'odt',
				'create_user_id' => $book->create_user_id
			]);

		UpdateBookFilesCount::dispatch($book);

		$book->refresh();

		$this->assertEquals(1, $book->files_count);
		$this->assertEquals(['odt'], $book->formats);
	}

	public function test2()
	{
		$book = factory(Book::class)
			->states('private', 'with_create_user')
			->create();

		$file = factory(BookFile::class)
			->states('odt', 'private')
			->create([
				'book_id' => $book->id,
				'format' => 'odt',
				'create_user_id' => $book->create_user_id
			]);

		$file->push();

		UpdateBookFilesCount::dispatch($book);

		$book->refresh();

		$this->assertEquals(1, $book->files_count);
		$this->assertEquals(['odt'], $book->formats);
	}
}
