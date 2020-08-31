<?php

namespace Litlife\BookConverter\Tests;

use Litlife\BookConverter\BookConverter;
use Litlife\BookConverter\Driver;
use PHPUnit\Framework\TestCase;

class OpenFileTest extends TestCase
{
	public function testOpenFile()
	{
		$driver = new Driver();
		$driver->inputFormats = ['txt'];

		$converter = new BookConverter();
		$converter->with($driver);

		$this->assertEquals($converter, $converter->open(__DIR__ . '/files/test.txt'));
	}

	public function testOpenFileWithoutExtension()
	{
		$driver = new Driver();
		$driver->inputFormats = ['txt'];

		$converter = new BookConverter();
		$converter->with($driver);

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('You must specify input file extension');

		$this->assertEquals($converter, $converter->open(__DIR__ . '/files/test'));
	}

	public function testOpenStream()
	{
		$driver = new Driver();
		$driver->inputFormats = ['txt'];

		$converter = new BookConverter();
		$converter->with($driver);

		$tmp = tmpfile();
		fputs($tmp, 'Hello world', 11);

		$this->assertEquals($converter, $converter->open($tmp, 'txt'));
		$this->assertTrue(file_exists($converter->getInputFile()->getPath()));
		$this->assertEquals(11, $converter->getInputFile()->getSize());
	}

	public function testOpenEmptyStream()
	{
		$driver = new Driver();
		$driver->inputFormats = ['txt'];

		$converter = new BookConverter();
		$converter->with($driver);

		$tmp = tmpfile();

		$this->expectException(\RuntimeException::class);
		$this->expectExceptionMessage('Zero bytes were written to the file');

		$this->assertEquals($converter, $converter->open($tmp, 'txt'));
	}

	public function testOpenStreamWithoutExtension()
	{
		$driver = new Driver();
		$driver->inputFormats = ['txt'];

		$converter = new BookConverter();
		$converter->with($driver);

		$tmp = tmpfile();
		fputs($tmp, 'Hello world', 100);

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('You must specify input resource extension');

		$this->assertEquals($converter, $converter->open($tmp));
	}

	public function testOpenFileOrResourceNotFound()
	{
		$driver = new Driver();
		$driver->inputFormats = ['txt'];

		$converter = new BookConverter();
		$converter->with($driver);

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('File or resource not found');

		$this->assertEquals($converter, $converter->open(uniqid()));
	}
}
