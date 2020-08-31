<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;

class ReplaceAsc194ToSpaceTest extends TestCase
{
	public function testInit()
	{
		$value = 'test   test';

		$response = $this->get('/?key=' . urlencode($value))
			->assertOk();

		$this->assertEquals('test   test', request()->input('key'));
	}

	public function testBugFixed()
	{
		$response = $this->get('/?page=1%F5%E4%E5')
			->assertOk();

		$this->assertEquals('1', request()->input('page'));
	}
}
