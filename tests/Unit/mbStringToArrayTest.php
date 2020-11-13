<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class mbStringToArrayTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test1()
    {
        $array = mbStringToArray('тест');

        $this->assertEquals(['т', 'е', 'с', 'т'], $array);
    }

    public function test2()
    {
        $array = mbStringToArray('тест тест');

        $this->assertEquals(['т', 'е', 'с', 'т', ' ', 'т', 'е', 'с', 'т'], $array);
    }

    public function test3()
    {
        $array = mbStringToArray('test');

        $this->assertEquals(['t', 'e', 's', 't'], $array);
    }

    public function test4()
    {
        $array = mbStringToArray('т'.chr(194).''.chr(194).'т');

        $this->assertEquals(['т', ' ', ' ', 'т'], $array);
    }

    public function test5()
    {
        $array = mbStringToArray('т'.chr(194).chr(160).'т');

        $this->assertEquals(['т', ' ', 'т'], $array);
    }

    public function test6()
    {
        $array = mbStringToArray('т'.chr(195).''.chr(196).'т');

        $this->assertEquals(['т', ' ', ' ', 'т'], $array);
    }

    public function testChina()
    {
        $array = mbStringToArray('測試');

        $this->assertEquals(['測', '試'], $array);
    }

    public function testHindi()
    {
        $array = mbStringToArray('परीक्षण');

        $this->assertEquals(['प', 'र', 'ी', 'क', '्', 'ष', 'ण'], $array);
    }

    public function testIsland()
    {
        $array = mbStringToArray('prófið');

        $this->assertEquals(['p', 'r', 'ó', 'f', 'i', 'ð'], $array);
    }
}
