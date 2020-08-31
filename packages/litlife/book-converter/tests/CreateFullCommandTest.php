<?php

namespace Litlife\BookConverter\Tests;

use Litlife\BookConverter\BookConverter;
use PHPUnit\Framework\TestCase;

class CreateFullCommandTest extends TestCase
{
	public function testCalibreCommand()
	{
		$converter = new BookConverter();
		$converter->with('calibre')
			->open(__DIR__ . '/files/test.txt')
			->setOutputExtension('txt');

		$this->assertEquals("ebook-convert '" . $converter->getInputFile()->getPath() . "' '" . $converter->getOutputFile()->getPath() . "'",
			$converter->createFullCommand());
	}
}
