<?php

namespace Tests\Unit\Book\Section;

use App\Section;
use PHPUnit\Framework\TestCase;

class SectionTitleTest extends TestCase
{
	public function testStripTags()
	{
		$section = new Section();
		$section->title = '<b>заголовок</b>';

		$this->assertEquals('заголовок', $section->title);
	}

	public function testTrim()
	{
		$section = new Section();
		$section->title = '   заголовок    ';

		$this->assertEquals('заголовок', $section->title);
	}

	public function testReplaceAsc192To32()
	{
		$section = new Section();
		$section->title = 'заголовок' . chr(194) . '' . chr(194) . '' . chr(194) . '' . chr(194) . '' . chr(194) . 'заголовок';

		$this->assertEquals('заголовок заголовок', $section->title);
	}

	public function testMaxLength()
	{
		$section = new Section();
		$section->title = 'заголовок заголовок заголовок заголовок заголовок заголовок заголовок заголовок заголовок заголовок заголовок заголовок заголовок';

		$this->assertEquals('заголовок заголовок заголовок заголовок заголовок заголовок заголовок заголовок заголовок заголовок', $section->title);
	}

	public function testManySpacesToOne()
	{
		$section = new Section();
		$section->title = 'заголовок   заголовок    заголовок';

		$this->assertEquals('заголовок заголовок заголовок', $section->title);
	}
}
