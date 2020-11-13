<?php

namespace Tests\Unit\NestedItems;

use PHPUnit\Framework\TestCase;

class NestedItemsIsChildOfTest extends TestCase
{
    public function testDefault()
    {
        $parent = new TestModel;
        $child = new TestModel;

        $this->assertFalse($parent->isChildOf($child));
    }

    public function testTrue()
    {
        $parent = new TestModel;
        $parent->id = 1;

        $child = new TestModel;
        $child->tree = ',1,';

        $this->assertTrue($child->isChildOf($parent));
    }

    public function testFalse()
    {
        $parent = new TestModel;
        $parent->id = 3;

        $child = new TestModel;
        $child->tree = ',4,5,';

        $this->assertFalse($child->isChildOf($parent));
    }

    public function testFalse2()
    {
        $parent = new TestModel;
        $parent->id = 4;

        $child = new TestModel;
        $child->tree = ',1,2,3,4,5,';

        $this->assertFalse($child->isChildOf($parent));
    }

    public function testTrue2()
    {
        $parent = new TestModel;
        $parent->id = 5;

        $child = new TestModel;
        $child->tree = ',1,2,3,4,5';

        $this->assertTrue($child->isChildOf($parent));
    }

    public function testFalse3()
    {
        $parent = new TestModel;
        $parent->id = 7;

        $child = new TestModel;
        $child->tree = ',1,2,3,4,57';

        $this->assertFalse($child->isChildOf($parent));
    }
}
