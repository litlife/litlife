<?php

namespace Tests\Feature\Book\Cover;

use App\Book;
use Tests\TestCase;

class BookCoverTest extends TestCase
{
    public function testShow()
    {
        $book = Book::factory()->with_cover()->create();

        $this->get(route('books.cover.show', ['book' => $book]))
            ->assertOk()
            ->assertViewIs('book.cover.show')
            ->assertViewHas('book', $book);
    }

    public function testShowAjax()
    {
        $book = Book::factory()->with_cover()->create();

        $this->ajax()
            ->get(route('books.cover.show', ['book' => $book]))
            ->assertOk();
    }

    public function testNotFound()
    {
        $book = Book::factory()->create();

        $this->get(route('books.cover.show', ['book' => $book]))
            ->assertNotFound();
    }
}
