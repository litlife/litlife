<?php

namespace Litlife\Fb2\Tests;

use Litlife\Fb2\Fb2;
use Litlife\Fb2\Fb2Description;
use Litlife\Fb2\Fb2TitleInfo;
use Litlife\Fb2\Tag;
use PHPUnit\Framework\TestCase;

class Fb2TagTest extends TestCase
{
	public function testGetXML()
	{
		$fb2 = new Fb2();
		$description = new Tag($fb2, 'description');

		$this->assertEquals('<description xmlns="http://www.gribuser.ru/xml/fictionbook/2.0"></description>', $description->getXML());
	}

	public function testHasChild()
	{
		$fb2 = new Fb2();

		$fb2->description()
			->create('test');

		$this->assertFalse($fb2->description()->hasChild('test2'));
		$this->assertTrue($fb2->description()->hasChild('test'));
	}

	public function testChildName()
	{
		$fb2 = new Fb2();

		$this->assertEquals(0, $fb2->description()->childs('test')->count());

		$fb2->description()->create('test')->setValue('value');
		$fb2->description()->create('test')->setValue('value');

		$this->assertEquals(2, $fb2->description()->childs('test')->count());
	}

	public function testGetFirstChildValue()
	{
		$fb2 = new Fb2();

		$fb2->description()->create('test')->setValue('value');

		$this->assertEquals('value', $fb2->description()->getFirstChildValue('test'));
		$this->assertEquals(null, $fb2->description()->getFirstChildValue('test2'));
	}

	public function testIsHaveImages()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test.fb2');

		$body = $fb2->getBodies()[0];

		$this->assertTrue($body->childs('section')->item(0)->isHaveImages());

		$this->assertFalse($body->childs('section')->item(1)->isHaveImages());
	}
}
