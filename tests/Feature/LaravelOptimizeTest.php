<?php

namespace Tests\Feature;

use Tests\TestCase;

class LaravelOptimizeTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function test()
	{
		$this->artisan('optimize')
			->assertExitCode(0)
			->expectsOutput('Files cached successfully!');

		$this->artisan('optimize:clear')
			->assertExitCode(0)
			->expectsOutput('Caches cleared successfully!');

		$this->assertTrue(true);
	}
}
