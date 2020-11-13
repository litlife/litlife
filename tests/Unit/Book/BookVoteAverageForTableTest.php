<?php

namespace Tests\Unit\Book;

use App\Book;
use PHPUnit\Framework\TestCase;

class BookVoteAverageForTableTest extends TestCase
{
    public function test10()
    {
        $book = new Book();
        $book->vote_average = 10;

        $this->assertEquals('10.0', (string) $book->getVoteAverageForTable());
    }

    public function test910()
    {
        $book = new Book();
        $book->vote_average = 9.1;

        $this->assertEquals('9.10', (string) $book->getVoteAverageForTable());
    }

    public function test901()
    {
        $book = new Book();
        $book->vote_average = 9.01;

        $this->assertEquals('9.01', (string) $book->getVoteAverageForTable());
    }

    public function test0()
    {
        $book = new Book();
        $book->vote_average = 0;

        $this->assertEquals('0', (string) $book->getVoteAverageForTable());
    }
}
