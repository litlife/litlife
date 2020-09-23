<?php

namespace Tests\Feature\Sequence;

use Tests\TestCase;

class SequenceTest extends TestCase
{
	public function testPerPage()
	{
		$response = $this->get(route('sequences', ['per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['sequences']->perPage());

		$response = $this->get(route('sequences', ['per_page' => 200]))
			->assertOk();

		$this->assertEquals(100, $response->original->gatherData()['sequences']->perPage());
	}
}
