<?php

namespace Tests\Unit\Book;

use App\Book;
use PHPUnit\Framework\TestCase;

class BookCommentsCount extends TestCase
{
    public function testCommentCount()
    {
        $book = new Book();
        $book->comments_count = 1;

        $this->assertEquals(1, $book->comment_count);
    }

    public function testIntval()
    {
        $book = new Book();
        $book->comments_count = '4sdfg';

        $this->assertEquals(4, $book->comment_count);
    }

    public function testNullToIntval()
    {
        $book = new Book();

        $this->assertEquals(0, $book->comment_count);
    }

    public function testCommentsCount()
    {
        $book = new Book();
        $book->comment_count = 4;

        $this->assertEquals(4, $book->comments_count);
    }
}
