<?php

namespace Tests\Feature\Artisan;

use App\Book;
use App\BookFile;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookParseAllWaitedTest extends TestCase
{
	public function testCommand()
	{
		$book = Book::factory()->create();

		$file = new BookFile;
		$file->zip = true;
		$file->open(__DIR__ . '/../Book/Books/test.fb2');
		$file->statusAccepted();
		$file->source = true;
		$book->files()->save($file);

		$book->parse->wait();
		$book->push();

		Artisan::call('book:parse_all_waited', ['last_book_id' => $book->id]);

		$book->refresh();

		$this->assertTrue($book->parse->isSucceed());
	}
}
