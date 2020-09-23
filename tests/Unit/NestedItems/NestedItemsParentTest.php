<?php

namespace Tests\Unit\NestedItems;

use PHPUnit\Framework\TestCase;

class NestedItemsParentTest extends TestCase
{
	public function testTrue()
	{
		$parent = new TestModel;
		$parent->id = 123;

		$model = new TestModel;
		$model->parent = $parent;

		$this->assertEquals(1, $model->getLevel());
		$this->assertEquals([123], $model->getTree());
	}

	public function testNull()
	{
		$ancestor = new TestModel;
		$ancestor->id = 1;

		$parent = new TestModel;
		$parent->parent = $ancestor;
		$parent->id = 2;

		$model = new TestModel;
		$model->parent = $parent;

		$this->assertEquals(2, $model->getLevel());
		$this->assertEquals([1, 2], $model->getTree());
	}
}
