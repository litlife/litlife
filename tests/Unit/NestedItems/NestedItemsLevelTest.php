<?php

namespace Tests\Unit\NestedItems;

use PHPUnit\Framework\TestCase;

class NestedItemsLevelTest extends TestCase
{
    public function test1()
    {
        $model = new TestModel;
        $model->tree = ',2234,';

        $this->assertEquals(1, $model->getLevel());
    }

    public function testNull()
    {
        $model = new TestModel;
        $model->tree = '';

        $this->assertEquals(0, $model->getLevel());
    }

    public function test2()
    {
        $model = new TestModel;
        $model->tree = '1,2,3';

        $this->assertEquals(3, $model->getLevel());
    }

    public function test3()
    {
        $model = new TestModel;
        $model->tree = ',1,3,4,';

        $this->assertEquals(3, $model->getLevel());
    }

    public function test4()
    {
        $model = new TestModel;
        $model->tree = '1,7,';

        $this->assertEquals(2, $model->getLevel());
    }

    public function test5()
    {
        $model = new TestModel;
        $model->tree = ',45,23,,,,,,,1,7,';

        $this->assertEquals(4, $model->getLevel());
    }

    public function test6()
    {
        $model = new TestModel;
        $model->tree = ',';

        $this->assertEquals(0, $model->getLevel());
    }
}
