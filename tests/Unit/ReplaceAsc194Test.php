<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ReplaceAsc194Test extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testReplace()
    {
        $string = 'test '.chr(194).' test';

        $this->assertStringContainsString(chr(194), $string);

        $string = replaceAsc194toAsc32($string);

        $this->assertStringContainsString('test', $string);
        $this->assertStringNotContainsString(chr(194), $string);
        $this->assertEquals('test   test', $string);
    }

    public function testReplace2()
    {
        $string = 'test '.chr(194).''.chr(194).''.chr(194).' test';

        $this->assertStringContainsString(chr(194), $string);

        $string = replaceAsc194toAsc32($string);

        $this->assertStringContainsString('test', $string);
        $this->assertStringNotContainsString(chr(194), $string);
        $this->assertEquals('test     test', $string);
    }

    public function testReplace3()
    {
        $string = 'текст'.chr(194).''.chr(194).'текст';

        $this->assertStringContainsString(chr(194), $string);

        $string = replaceAsc194toAsc32($string);

        $this->assertStringNotContainsString(chr(194), $string);
        $this->assertEquals('текст  текст', $string);
    }

    public function testReplace4()
    {
        // https://litlife.club/posts/691424/go_to

        $original_string = 'тест тест';

        $this->assertStringContainsString(chr(194), $original_string);

        $string = replaceAsc194toAsc32($original_string);

        $this->assertStringNotContainsString(chr(194), $string);

        $this->assertEquals('тест тест', $string);
    }

    public function testReplace5()
    {
        $original_string = ' ';

        $this->assertStringContainsString(chr(194), $original_string);

        $string = replaceAsc194toAsc32($original_string);

        $this->assertStringNotContainsString(chr(194), $string);

        $this->assertEquals(' ', $string);
    }

    public function testReplace6()
    {
        $original_string = '   ';

        $this->assertStringContainsString(chr(194), $original_string);

        $string = replaceAsc194toAsc32($original_string);

        $this->assertStringNotContainsString(chr(194), $string);

        $this->assertEquals('   ', $string);
    }
}
