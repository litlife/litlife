<?php

namespace Tests\Feature\Book\File;

use App\BookFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class BookFileSentParsePagesTest extends TestCase
{

	public function testSentParsePagesFalseCantParsed()
	{
		config(['litlife.book_allowed_file_extensions' => ['fb2']]);

		$file = factory(BookFile::class)
			->states('fb2')
			->create();

		DB::table('book_files')
			->where('id', $file->id)
			->update(['format' => 'odt']);

		$file->refresh();

		$this->assertFalse($file->sentParsePages());
	}

	public function testSentParsePagesFalseParseWaited()
	{
		config(['litlife.book_allowed_file_extensions' => ['odt']]);

		$file = factory(BookFile::class)
			->states('odt')
			->create();

		$file->book->parse->wait();
		$file->push();
		$file->refresh();

		$this->assertFalse($file->sentParsePages());
	}

	public function testSentParsePagesFalseParseFailed()
	{
		config(['litlife.book_allowed_file_extensions' => ['odt']]);

		$file = factory(BookFile::class)
			->states('odt')
			->create();

		$file->book->parse->failed([]);
		$file->push();
		$file->refresh();

		$this->assertTrue($file->sentParsePages());
	}

	public function testSentParsePagesFalseParseStarted()
	{
		config(['litlife.book_allowed_file_extensions' => ['odt']]);

		$file = factory(BookFile::class)
			->states('odt')
			->create();

		$file->book->parse->start();
		$file->push();
		$file->refresh();

		$this->assertFalse($file->sentParsePages());
	}

	public function testSentParsePages()
	{
		config(['litlife.book_allowed_file_extensions' => ['odt']]);

		$file = factory(BookFile::class)
			->states('odt')
			->create();

		$book = $file->book;
		$book->parse->reset();
		$book->push();

		$this->assertTrue($file->sentParsePages());

		$this->assertEquals(2, $book->parses()->count());

		$file->refresh();
		$book->refresh();

		$this->assertTrue($book->is($book->parse->book));
		$this->assertTrue($file->isSource());
		$this->assertTrue($book->parse->isWait());
		$this->assertTrue($book->parse->isParseOnlyPages());
	}
}
