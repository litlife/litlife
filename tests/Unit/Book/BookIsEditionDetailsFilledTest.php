<?php

namespace Tests\Unit\Book;

use App\Book;
use PHPUnit\Framework\TestCase;

class BookIsEditionDetailsFilledTest extends TestCase
{
	public function testIfNothingFilled()
	{
		$book = new Book();

		$this->assertFalse($book->isEditionDetailsFilled());
	}

	public function testIfPublisherFilled()
	{
		$book = new Book();
		$book->pi_pub = 'Test';

		$this->assertTrue($book->isEditionDetailsFilled());
	}

	public function testIfCityFilled()
	{
		$book = new Book();
		$book->pi_city = 'Test';

		$this->assertTrue($book->isEditionDetailsFilled());
	}

	public function testIfYearFilled()
	{
		$book = new Book();
		$book->pi_year = '4567';

		$this->assertTrue($book->isEditionDetailsFilled());
	}

	public function testIfIsbnFilled()
	{
		$book = new Book();
		$book->pi_isbn = 'Test';

		$this->assertTrue($book->isEditionDetailsFilled());
	}

	public function testIfYearZero()
	{
		$book = new Book();
		$book->pi_year = '0';

		$this->assertFalse($book->isEditionDetailsFilled());
	}

	public function testIfIsbnZero()
	{
		$book = new Book();
		$book->pi_isbn = '  0';

		$this->assertFalse($book->isEditionDetailsFilled());
	}
}
