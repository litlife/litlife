<?php

namespace Tests\Feature\Book\File;

use App\Author;
use App\Book;
use Tests\TestCase;

class BookFileGetNameForBookFileTest extends TestCase
{
	public function testNameForBookFile()
	{
		$book = Book::factory()->without_any_authors()->create();

		$this->assertEquals('Название книги', $book->getNameForBookFile());

		$author = Author::factory()->create([
				'first_name' => 'Имя',
				'last_name' => 'Фамилия',
				'middle_name' => 'Отчество',
				'nickname' => 'Ник'
			]);

		$book->writers()->sync([$author->id]);
		$book->translators()->sync([$author->id]);
		$book->refresh();

		$this->assertEquals('Фамилия Имя Ник Название книги', $book->getNameForBookFile());

		$book->redaction = 5;
		$book->save();
		$book->refresh();

		$this->assertEquals('Фамилия Имя Ник Название книги r5', $book->getNameForBookFile());
	}
}
