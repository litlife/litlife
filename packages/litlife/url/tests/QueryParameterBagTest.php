<?php

namespace Litlife\Url\Tests;

use Litlife\Url\QueryParameterBag;
use PHPUnit\Framework\TestCase;

class QueryParameterBagTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testEmptyKey()
	{
		$array = QueryParameterBag::fromString('test=test&page=1&');

		$this->assertEquals('test=test&page=1', (string)$array);
	}

	public function testArray()
	{
		$array = QueryParameterBag::fromString('test%5B%5D=1&test%5B%5D=2');

		$this->assertEquals('test%5B%5D=1&test%5B%5D=2', (string)$array);
	}
}
