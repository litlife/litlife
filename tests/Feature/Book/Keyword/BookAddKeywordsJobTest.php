<?php

namespace Tests\Feature\Book\Keyword;

use App\Book;
use App\Jobs\Book\BookAddKeywordsJob;
use App\Jobs\Book\BookRemoveKeywordsJob;
use App\Keyword;
use Tests\TestCase;

class BookAddKeywordsJobTest extends TestCase
{
    public function testAddNew()
    {
        $book = Book::factory()->with_writer()->create();

        $keyword = Keyword::factory()->create();

        BookAddKeywordsJob::dispatch($book, [$keyword->text]);

        $book->refresh();

        $book_keywords = $book->book_keywords()->get();

        $this->assertEquals(1, $book_keywords->count());
        $this->assertTrue($book_keywords->first()->keyword->is($keyword));
    }

    public function testAddNewIfOtherExists()
    {
        $book = Book::factory()->with_writer()->with_keyword()->create();

        $keyword = Keyword::factory()->create();

        $book_keyword = $book->book_keywords()->first();

        BookAddKeywordsJob::dispatch($book, [$book_keyword->keyword->text, $keyword->text]);

        $this->assertEquals(2, $book->book_keywords()->count());
    }
}
