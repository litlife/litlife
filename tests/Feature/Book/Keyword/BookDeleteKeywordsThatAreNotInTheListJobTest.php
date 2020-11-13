<?php

namespace Tests\Feature\Book\Keyword;

use App\Book;
use App\Jobs\Book\BookDeleteKeywordsThatAreNotInTheListJob;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookDeleteKeywordsThatAreNotInTheListJobTest extends TestCase
{
    public function testRemoveThatNotInList()
    {
        $book = Book::factory()->with_writer()->with_keyword()->create();

        BookDeleteKeywordsThatAreNotInTheListJob::dispatch($book, []);

        $this->assertEquals(0, $book->book_keywords()->count());
    }

    public function testDontRemoveThatInList()
    {
        $book = Book::factory()->with_writer()->with_keyword()->create();

        BookDeleteKeywordsThatAreNotInTheListJob::dispatch($book, [$book->book_keywords()->first()->keyword->text]);

        $this->assertEquals(1, $book->book_keywords()->count());
    }

    public function testDeleteBookKeywordIfKeywordDeleted()
    {
        $book = Book::factory()->with_writer()->with_keyword()->create();

        $book_keyword = $book->book_keywords()->first();
        $keyword = $book_keyword->keyword;
        $keyword->delete();

        $text = Str::random(8);

        BookDeleteKeywordsThatAreNotInTheListJob::dispatch($book, [$text]);

        $this->assertEquals(0, $book->book_keywords()->count());
    }
}
