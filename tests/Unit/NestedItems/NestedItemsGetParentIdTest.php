<?php

namespace Tests\Unit\NestedItems;

use PHPUnit\Framework\TestCase;

class NestedItemsGetParentIdTest extends TestCase
{
    public function testDefault()
    {
        $parent = new TestModel;

        $this->assertEquals(null, $parent->getParentId());
    }

    public function test1()
    {
        $parent = new TestModel;
        $parent->id = 123;

        $model = new TestModel;
        $model->parent = $parent;

        $this->assertEquals(123, $model->getParentId());
    }

    public function test2()
    {
        $ancestor = new TestModel;
        $ancestor->id = 123;

        $parent = new TestModel;
        $parent->parent = $ancestor;
        $parent->id = 124;

        $model = new TestModel;
        $model->parent = $parent;

        $this->assertEquals(124, $model->getParentId());
    }
}
