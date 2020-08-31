<?php

namespace Litlife\BookConverter\Tests;

use Litlife\BookConverter\BookConverter;
use Litlife\BookConverter\BookConverterMock;
use PHPUnit\Framework\TestCase;

class TempDirTest extends TestCase
{
	public function testCreateAndGet()
	{
		$converter = new BookConverter();

		$this->assertTrue($converter->createTempDir());

		$folder = $converter->getTempDir();

		$this->assertTrue(file_exists($folder));
		$this->assertTrue(is_dir($folder));
		$this->assertTrue(is_readable($folder));
	}
}
