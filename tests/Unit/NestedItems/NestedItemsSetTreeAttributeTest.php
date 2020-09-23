<?php

namespace Tests\Unit\NestedItems;

use PHPUnit\Framework\TestCase;

class NestedItemsSetTreeAttributeTest extends TestCase
{
	public function test1()
	{
		$model = new TestModel;
		$model->tree = ',2234,';

		$this->assertEquals([2234], $model->getTree());
	}

	public function test2()
	{
		$model = new TestModel;
		$model->tree = '1,,,,2';

		$this->assertEquals([1, 2], $model->getTree());
	}

	public function test3()
	{
		$model = new TestModel;
		$model->tree = '1,,0,0,2';

		$this->assertEquals([1, 2], $model->getTree());
	}

	public function testSetEmptyTree()
	{
		$model = new TestModel;
		$model->tree = null;

		$this->assertEquals([], $model->getTree());
	}
}
