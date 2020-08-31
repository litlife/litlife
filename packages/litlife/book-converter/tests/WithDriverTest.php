<?php

namespace Litlife\BookConverter\Tests;

use Litlife\BookConverter\AbiwordDriver;
use Litlife\BookConverter\BookConverter;
use Litlife\BookConverter\CalibreDriver;
use Litlife\BookConverter\Driver;
use PHPUnit\Framework\TestCase;

class WithDriverTest extends TestCase
{
	public function testAbiword()
	{
		$converter = new BookConverter();

		$this->assertEquals($converter, $converter->with('abiword'));

		$this->assertInstanceOf(AbiwordDriver::class, $converter->getDriver());
	}

	public function testCalibre()
	{
		$converter = new BookConverter();

		$this->assertEquals($converter, $converter->with('calibre'));

		$this->assertInstanceOf(CalibreDriver::class, $converter->getDriver());
	}

	public function testDriver()
	{
		$driver = new Driver();

		$converter = new BookConverter();

		$this->assertEquals($converter, $converter->with($driver));

		$this->assertInstanceOf(Driver::class, $converter->getDriver());
	}

	public function testDriverNotSupported()
	{
		$driver = uniqid();

		$this->expectException(\InvalidArgumentException::class);
		$this->expectExceptionMessage('The specified driver "' . $driver . '" is not supported');

		$converter = new BookConverter();
		$converter->with($driver);
	}
}
