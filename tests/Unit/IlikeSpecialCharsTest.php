<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class IlikeSpecialCharsTest extends TestCase
{
    public function testUnderScore()
    {
        $string = 'test___test@mail.ru';

        $this->assertEquals('test\_\_\_test@mail.ru', ilikeSpecialChars($string));
    }

    public function testPercent()
    {
        $string = 'test%%%test@mail.ru';

        $this->assertEquals('test\%\%\%test@mail.ru', ilikeSpecialChars($string));
    }

    public function testOtherSymbols()
    {
        $string = '!@#$%^&*()[],.?><~`/*-+';

        $this->assertEquals('!@#$\%^&*()[],.?><~`/*-+', ilikeSpecialChars($string));
    }
}
