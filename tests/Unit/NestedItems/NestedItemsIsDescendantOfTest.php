<?php

namespace Tests\Unit\NestedItems;

use PHPUnit\Framework\TestCase;

class NestedItemsIsDescendantOfTest extends TestCase
{
	public function testFalse()
	{
		$parent = new TestModel;
		$descendant = new TestModel;

		$this->assertFalse($parent->isDescendantOf($descendant));
	}

	public function testTrue()
	{
		$parent = new TestModel;
		$parent->id = 1;

		$descendant = new TestModel;
		$descendant->tree = ',1,';

		$this->assertTrue($descendant->isDescendantOf($parent));
	}

	public function testFalse2()
	{
		$parent = new TestModel;
		$parent->id = 3;

		$descendant = new TestModel;
		$descendant->tree = ',1,';

		$this->assertFalse($descendant->isDescendantOf($parent));
	}

	public function testTrue2()
	{
		$parent = new TestModel;
		$parent->id = 53;

		$descendant = new TestModel;
		$descendant->tree = ',1,34345,5353,53,65656,';

		$this->assertTrue($descendant->isDescendantOf($parent));
	}

	public function testFalse3()
	{
		$parent = new TestModel;
		$parent->id = 53;

		$descendant = new TestModel;
		$descendant->tree = ',1,34345,5353,65656,';

		$this->assertFalse($descendant->isDescendantOf($parent));
	}
}
