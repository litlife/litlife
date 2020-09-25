<?php

namespace Tests\Feature\Book\Section;

use App\Section;
use Tests\TestCase;

class SectionIsChildTagOnlyOneTest extends TestCase
{
	public function testIsChildTagOnlyOne()
	{
		$section = new Section();

		$xhtml = '<body><div><div><div><div><p>текст</p><p>текст2</p></div></div></div></div></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertTrue($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('div')->item(0)));

		$xhtml = '<body><div><p>текст</p><p>текст2</p></div></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('div')->item(0)));

		$xhtml = '<body><p>текст</p><p>текст2</p></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('body')->item(0)));

		$xhtml = '<body><p>текст</p></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('body')->item(0)));

		$xhtml = '<body><div>текст</div></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('body')->item(0)));

		$xhtml = '<body><div>текст</div><div>текст</div></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('body')->item(0)));

		$xhtml = '<body><div><div><p>текст</p></div><div><p>текст</p></div></div></body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertTrue($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('div')->item(0)));
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('div')->item(1)));
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('div')->item(2)));

		$xhtml = '<body>test</body>';
		$section->dom()->loadHTML('<meta http-equiv="Content-Type" content="text/html; charset=utf-8">' . $xhtml);
		$section->xpath(true);
		$this->assertFalse($section->isChildTagOnlyOne($section->dom()->getElementsByTagName('body')->item(0)));
	}
}
