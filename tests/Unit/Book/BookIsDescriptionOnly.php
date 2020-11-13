<?php

namespace Tests\Unit\Book;

use App\Book;
use PHPUnit\Framework\TestCase;

class BookIsDescriptionOnly extends TestCase
{
    public function testFalseIfFilesCountGreaterThanZero()
    {
        $book = new Book();
        $book->files_count = 1;

        $this->assertFalse($book->isDescriptionOnly());
    }

    public function testFalseIfSectionsCountGreaterThanZero()
    {
        $book = new Book();
        $book->sections_count = 1;

        $this->assertFalse($book->isDescriptionOnly());
    }

    public function testFalseIfPagesCountGreaterThanZero()
    {
        $book = new Book();
        $book->page_count = 1;

        $this->assertFalse($book->isDescriptionOnly());
    }

    public function testTrue()
    {
        $book = new Book();
        $book->files_count = 0;
        $book->sections_count = 0;
        $book->page_count = 0;

        $this->assertTrue($book->isDescriptionOnly());
    }
}
