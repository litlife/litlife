<?php

namespace Litlife\IdDirname\Tests;

use Litlife\IdDirname\Exceptions\InvalidArgumentException;
use Litlife\IdDirname\IdDirname;
use PHPUnit\Framework\TestCase;

class IdDirnameTest extends TestCase
{
	public function testSplitToArray()
	{
		$class = new IdDirname(12345678);
		$class->setMaxNumberOfDigits(3);
		$this->assertEquals([12, 345, 678], $class->splitToArray());

		$class = new IdDirname(2345678);
		$class->setMaxNumberOfDigits(3);
		$this->assertEquals([2, 345, 678], $class->splitToArray());

		$class = new IdDirname(2345678);
		$class->setMaxNumberOfDigits(4);
		$this->assertEquals([234, 5678], $class->splitToArray());

		$class = new IdDirname(678);
		$class->setMaxNumberOfDigits(4);
		$this->assertEquals([678], $class->splitToArray());
	}

	public function testIfIDZero()
	{
		$this->expectExceptionObject(new InvalidArgumentException('ID must be greater than zero'));

		$class = new IdDirname(0);
	}

	public function testGetSalt()
	{
		$class = new IdDirname(12345678);
		$this->assertEquals(678, $class->getSalt());

		$class = new IdDirname(1);
		$this->assertEquals(1, $class->getSalt());

		$class = new IdDirname(568);
		$this->assertEquals(568, $class->getSalt());
	}

	public function testGetSaltEncoded()
	{
		$class = new IdDirname(12345678);
		$this->assertEquals($class->encode(678), $class->getSaltEncoded());

		$class = new IdDirname(1);
		$this->assertEquals($class->encode(1), $class->getSaltEncoded());

		$class = new IdDirname(568);
		$this->assertEquals($class->encode(568), $class->getSaltEncoded());
	}

	public function testGetDirnameArray()
	{
		$class = new IdDirname(12345678);
		$this->assertEquals([
			$class->encode(12),
			$class->encode(345),
		], $class->getDirnameArrayEncoded());

		$class = new IdDirname(750);
		$this->assertEquals([], $class->getDirnameArrayEncoded());
	}

	public function testDefaultMaxNumberOfDigits()
	{
		$class = new IdDirname(12345678);

		$this->assertEquals(3, $class->getMaxNumberOfDigits());
	}

	public function testSetGetMaxNumberOfDigits()
	{
		$class = new IdDirname(12345678);
		$class->setMaxNumberOfDigits(4);

		$this->assertEquals(4, $class->getMaxNumberOfDigits());
	}

	public function testEncodeDictionary()
	{
		$class = new IdDirname(1);

		$this->assertEquals('7vd', $class->encode(12345));
		$this->assertEquals('z4n2', $class->encode(78956));
	}

	public function testDecodeDefaultDictionary()
	{
		$class = new IdDirname(1);

		$this->assertEquals(7274, $class->decode('nk7'));
		$this->assertEquals(280285, $class->decode('gnd9'));
		$this->assertEquals(7848, $class->decode(558));
	}

	public function testEncodeDecodeEquals()
	{
		$class = new IdDirname(1);

		for ($a = 0; $a < 10; $a++) {
			$number = rand(1, 1000000);

			$this->assertEquals($number, $class->decode($class->encode($number)));
		}
	}
}
