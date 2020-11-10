<?php

namespace Tests\Feature\Artisan;

use App\ViewCount;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ClearBookViewCountsPeriodTest extends TestCase
{
	public function testClearPeriodDay()
	{
		$book_view_count = ViewCount::factory()->create(['day' => '1',
				'week' => '2',
				'month' => '3',
				'year' => '4',
				'all' => '5'
			]);

		$book = $book_view_count->book;

		$this->assertNotNull(ViewCount::where('day', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('week', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('month', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('year', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('all', '>', '0')->first());

		$this->assertEquals(1, $book->view_count->day);
		$this->assertEquals(2, $book->view_count->week);
		$this->assertEquals(3, $book->view_count->month);
		$this->assertEquals(4, $book->view_count->year);
		$this->assertEquals(5, $book->view_count->all);

		Artisan::call('clear:book_view_counts_period', ['period' => 'day']);
		$book->refresh();

		$this->assertNull(ViewCount::where('day', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('week', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('month', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('year', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('all', '>', '0')->first());
		$this->assertEquals(0, $book->view_count->day);
		$this->assertEquals(2, $book->view_count->week);
		$this->assertEquals(3, $book->view_count->month);
		$this->assertEquals(4, $book->view_count->year);
		$this->assertEquals(5, $book->view_count->all);

		Artisan::call('clear:book_view_counts_period', ['period' => 'week']);
		$book->refresh();

		$this->assertNull(ViewCount::where('day', '>', '0')->first());
		$this->assertNull(ViewCount::where('week', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('month', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('year', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('all', '>', '0')->first());
		$this->assertEquals(0, $book->view_count->day);
		$this->assertEquals(0, $book->view_count->week);
		$this->assertEquals(3, $book->view_count->month);
		$this->assertEquals(4, $book->view_count->year);
		$this->assertEquals(5, $book->view_count->all);

		Artisan::call('clear:book_view_counts_period', ['period' => 'month']);
		$book->refresh();

		$this->assertNull(ViewCount::where('day', '>', '0')->first());
		$this->assertNull(ViewCount::where('week', '>', '0')->first());
		$this->assertNull(ViewCount::where('month', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('year', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('all', '>', '0')->first());
		$this->assertEquals(0, $book->view_count->day);
		$this->assertEquals(0, $book->view_count->week);
		$this->assertEquals(0, $book->view_count->month);
		$this->assertEquals(4, $book->view_count->year);
		$this->assertEquals(5, $book->view_count->all);

		Artisan::call('clear:book_view_counts_period', ['period' => 'year']);
		$book->refresh();

		$this->assertNull(ViewCount::where('day', '>', '0')->first());
		$this->assertNull(ViewCount::where('week', '>', '0')->first());
		$this->assertNull(ViewCount::where('month', '>', '0')->first());
		$this->assertNull(ViewCount::where('year', '>', '0')->first());
		$this->assertNotNull(ViewCount::where('all', '>', '0')->first());
		$this->assertEquals(0, $book->view_count->day);
		$this->assertEquals(0, $book->view_count->week);
		$this->assertEquals(0, $book->view_count->month);
		$this->assertEquals(0, $book->view_count->year);
		$this->assertEquals(5, $book->view_count->all);
	}
}
