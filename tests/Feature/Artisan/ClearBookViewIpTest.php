<?php

namespace Tests\Feature\Artisan;

use App\BookViewIp;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ClearBookViewIpTest extends TestCase
{
	public function testViewClear()
	{
		$book_view_ip = factory(BookViewIp::class)
			->create();

		$book = $book_view_ip->book;

		$this->assertNotNull(BookViewIp::first());

		Artisan::call('clear:book_view_ip');

		$this->assertNull(BookViewIp::first());
	}
}
