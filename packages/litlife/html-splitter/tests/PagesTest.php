<?php

namespace Litlife\HtmlSplitter\Tests;

use Litlife\HtmlSplitter\Page;
use Litlife\HtmlSplitter\Pages;
use PHPUnit\Framework\TestCase;

class PagesTest extends TestCase
{
	public function testCurrent()
	{
		$pages = new Pages();

		$this->assertInstanceOf(Page::class, $pages->current());
	}

	public function testKey()
	{
		$pages = new Pages(1);
		$this->assertEquals(1, $pages->key());

		$pages = new Pages(2);
		$this->assertEquals(2, $pages->key());
	}

	public function testNext()
	{
		$pages = new Pages(1);

		$pages->next();

		$this->assertEquals(2, $pages->key());

		$pages->next();

		$this->assertEquals(3, $pages->key());
	}

	public function testValid()
	{
		$pages = new Pages(1);

		$this->assertFalse($pages->valid());

		$pages->current();

		$this->assertTrue($pages->valid());

		$pages->delete($pages->key());

		$this->assertFalse($pages->valid());
	}

	public function testRewind()
	{
		$pages = new Pages(3);

		$pages->next();

		$this->assertEquals(4, $pages->key());

		$pages->rewind();

		$this->assertEquals(3, $pages->key());
	}
}
