<?php

namespace Tests\Feature\Forum\Topic;

use App\Topic;
use Tests\TestCase;

class TopicTest extends TestCase
{
	public function testFulltextSearch()
	{
		$author = Topic::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}
}
