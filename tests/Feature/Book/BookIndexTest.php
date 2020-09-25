<?php

namespace Tests\Feature;

use Tests\TestCase;

class BookIndexTest extends TestCase
{
	public function testIndexHttp()
	{
		$this->get(route('books'))
			->assertOk();
	}
}
