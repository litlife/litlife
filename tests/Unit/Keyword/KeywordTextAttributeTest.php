<?php

namespace Tests\Unit\Keyword;

use App\Keyword;
use PHPUnit\Framework\TestCase;

class KeywordTextAttributeTest extends TestCase
{
    public function testUcfirst()
    {
        $keyword = new Keyword();
        $keyword->text = 'привет';

        $this->assertEquals('Привет', $keyword->text);
    }

    public function testTrim()
    {
        $keyword = new Keyword();
        $keyword->text = '  привет   ';

        $this->assertEquals('Привет', $keyword->text);
    }

    public function testSpacesToOne()
    {
        $keyword = new Keyword();
        $keyword->text = 'привет    привет';

        $this->assertEquals('Привет привет', $keyword->text);
    }

    public function testRemoveDotsAtEnd()
    {
        $keyword = new Keyword();
        $keyword->text = 'привет.';

        $this->assertEquals('Привет', $keyword->text);
    }
}
