<?php

namespace Tests\Unit\Book;

use App\Book;
use App\Enums\StatusEnum;
use PHPUnit\Framework\TestCase;

class BookTest extends TestCase
{
	public function testDefaultAttributes()
	{
		$book = new Book();

		$this->assertEquals(StatusEnum::Private, $book->status);
		$this->assertEquals(true, $book->online_read_new_format);
	}
}
