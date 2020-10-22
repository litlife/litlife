<?php

namespace Litlife\Epub\Tests;

use Litlife\Epub\Epub;
use PhpZip\Exception\ZipEntryNotFoundException;

class IgnoreMissingFilesTest extends TestCase
{
	public function testIfDisabledThrowException()
	{
		$this->expectException(ZipEntryNotFoundException::class);
		$this->expectExceptionMessage('Zip Entry "OEBPS/Images/test.png" was not found in the archive.');

		$epub = new Epub();
		$epub->ignoreMissingFiles = false;
		$epub->setFile(__DIR__ . '/books/test_missing_files.epub');
	}

	public function testIfEnabledIgnoreException()
	{
		$epub = new Epub();
		$epub->ignoreMissingFiles = true;
		$epub->setFile(__DIR__ . '/books/test_missing_files.epub');

		$this->assertNotContains('OEBPS/Images/test.png', $epub->getAllFilesList());
	}
}
