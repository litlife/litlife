<?php

namespace Tests\Unit\NestedItems;

use PHPUnit\Framework\TestCase;

class NestedItemsIsRootTest extends TestCase
{
    public function test1()
    {
        $model = new TestModel;
        $model->level = 0;

        $this->assertTrue($model->isRoot());
    }

    public function test2()
    {
        $model = new TestModel;
        $model->level = null;

        $this->assertTrue($model->isRoot());
    }

    public function test3()
    {
        $model = new TestModel;
        $model->level = 2;

        $this->assertFalse($model->isRoot());
    }

    public function test4()
    {
        $model = new TestModel;
        $model->level = 3;

        $this->assertFalse($model->isRoot());
    }
}
