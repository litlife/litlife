<?php

namespace Litlife\BookConverter\Tests;

use Litlife\BookConverter\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
	public function testGetPath()
	{
		$file = new File(__DIR__ . '/files/test.txt');

		$this->assertEquals(__DIR__ . '/files/test.txt', $file->getPath());
		$this->assertEquals(__DIR__ . '/files/test.txt', $file->getFilePath());
	}

	public function testGetStream()
	{
		$file = new File(__DIR__ . '/files/test.txt');

		$this->assertTrue(is_resource($file->getStream()));
		$this->assertTrue(is_resource($file->getFileStream()));
	}

	public function testGetExtension()
	{
		$file = new File(__DIR__ . '/files/test.txt');

		$this->assertEquals('txt', $file->getExtension());
	}

	public function testGetSize()
	{
		$file = new File(__DIR__ . '/files/test.txt');

		$this->assertEquals(11, $file->getSize());
	}

	public function testPutContentsFromResource()
	{
		$tmp = tmpfile();
		fputs($tmp, 'Data', 4);

		$tmp2 = tmpfile();

		$file = new File(stream_get_meta_data($tmp2)['uri']);
		$file->putContentsFromResource($tmp);

		$this->assertEquals('Data', file_get_contents($file->getPath()));
	}
}
