<?php

namespace Litlife\Fb2\Tests;

use Litlife\Fb2\Fb2;
use Litlife\Fb2\Fb2Description;
use Litlife\Fb2\Fb2TitleInfo;
use PHPUnit\Framework\TestCase;

class Fb2SectionTest extends TestCase
{
	public function testIsHaveInnerSections()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test.fb2');

		$body = $fb2->getBodies()[0];

		$section = $body->childs('section')->item(3);

		$this->assertFalse($section->isHaveInnerSections());

		$section = $body->childs('section')->item(4);

		$this->assertTrue($section->isHaveInnerSections());
	}

	public function testGetSectionsCount()
	{
		$fb2 = new Fb2();
		$fb2->setFile(__DIR__ . '/books/test.fb2');

		$body = $fb2->getBodies()[0];

		$section = $body->childs('section')->item(3);

		$this->assertEquals(0, $section->getSectionsCount());

		$section = $body->childs('section')->item(4);

		$this->assertEquals(1, $section->getSectionsCount());
	}
}
