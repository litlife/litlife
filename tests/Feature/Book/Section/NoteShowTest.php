<?php

namespace Tests\Feature\Book\Section;

use App\Book;
use Tests\TestCase;

class NoteShowTest extends TestCase
{
    public function testNotFound()
    {
        $book = Book::factory()->accepted()->create();

        $this->get(route('books.notes.show', ['book' => $book, 'note' => rand(100, 1000)]))
            ->assertNotFound();
    }
}
