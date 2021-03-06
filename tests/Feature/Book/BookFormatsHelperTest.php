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
        $book = Book::factory()->accepted()->with_create_user()->create();

        $file = BookFile::factory()
            ->odt()
            ->accepted()
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
        $book = Book::factory()->private()->with_create_user()->create();

        $file = BookFile::factory()
            ->odt()
            ->private()
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
