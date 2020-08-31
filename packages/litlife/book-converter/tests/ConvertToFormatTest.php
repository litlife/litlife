<?php

namespace Litlife\BookConverter\Tests;

use Litlife\BookConverter\BookConverter;
use Litlife\BookConverter\Driver;
use Litlife\BookConverter\File;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ConvertToFormatTest extends TestCase
{
	public function testConvert()
	{
		$driver = new Driver();
		$driver->inputFormats = ['txt'];
		$driver->outputFormats = ['txt'];

		$converter = new BookConverter();
		$converter->with($driver);
		$converter->open(__DIR__ . '/files/test.txt');

		$process = $this->createMock(Process::class);

		$process->method('setTimeout')
			->will($this->returnSelf());

		$process->method('isSuccessful')
			->will($this->returnValue(true));

		$this->assertInstanceOf(File::class, $converter->convertToFormat('txt', $process));
	}
}
