<?php

namespace Tests\Feature\Artisan;

use App\Book;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class UserBooksProcessingTest extends TestCase
{
	public function testPublish()
	{
		Storage::fake('public');

		$book = factory(Book::class)
			->states('private', 'parsed')
			->create();

		$user = $book->create_user;

		$this->artisan('user:books_processing', ['user_id' => $user->id,
			'--publish' => true,
			'--delete_files' => false,
			'--disable_download_access' => false
		])->assertExitCode(0);

		$book->refresh();

		$this->assertTrue($book->isAccepted());
	}

	public function testDeleteFiles()
	{
		Storage::fake('public');

		$book = factory(Book::class)
			->states('with_file', 'parsed')
			->create();

		$user = $book->create_user;
		$file = $book->files()->first();

		$this->assertTrue($file->exists());

		$this->artisan('user:books_processing', ['user_id' => $user->id,
			'--publish' => false,
			'--delete_files' => true,
			'--disable_download_access' => false
		])->assertExitCode(0);

		$file->refresh();

		$this->assertFalse($file->exists());
	}

	public function testDisableDownloadAccess()
	{
		Storage::fake('public');

		$book = factory(Book::class)
			->states('with_writer', 'parsed', 'with_read_and_download_access')
			->create();

		$user = $book->create_user;

		$this->assertTrue($book->isDownloadAccess());

		$this->artisan('user:books_processing', ['user_id' => $user->id,
			'--publish' => false,
			'--delete_files' => false,
			'--disable_download_access' => true
		])->assertExitCode(0);

		$book->refresh();

		$this->assertFalse($book->isDownloadAccess());
	}

	public function testBookShouldBeParsed()
	{
		Storage::fake('public');

		$book = factory(Book::class)->create();
		$book->parse->wait();
		$book->push();

		$user = $book->create_user;

		$this->assertTrue($book->fresh()->isDownloadAccess());

		$this->artisan('user:books_processing', ['user_id' => $user->id, '--disable_download_access' => true])
			->assertExitCode(0);

		$this->assertTrue($book->fresh()->isDownloadAccess());

		$book->parse->reset();
		$book->push();

		$this->artisan('user:books_processing', ['user_id' => $user->id, '--disable_download_access' => true])
			->assertExitCode(0);

		$this->assertTrue($book->fresh()->isDownloadAccess());

		$book->parse->start();
		$book->push();

		$this->artisan('user:books_processing', ['user_id' => $user->id, '--disable_download_access' => true])
			->assertExitCode(0);

		$this->assertTrue($book->fresh()->isDownloadAccess());

		$book->parse->failed([]);
		$book->push();

		$this->artisan('user:books_processing', ['user_id' => $user->id, '--disable_download_access' => true])
			->assertExitCode(0);

		$this->assertTrue($book->fresh()->isDownloadAccess());
	}
}
