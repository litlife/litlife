<?php

namespace Tests\Feature\Book;

use App\Book;
use App\BookViewIp;
use App\Section;
use App\User;
use App\ViewCount;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class BookViewTest extends TestCase
{
	public function testView()
	{
		$section = factory(Section::class)
			->create();

		$book = $section->book;
		$book->statusAccepted();
		$book->push();
		$book->refresh();

		$this->assertEquals(0, $book->view_count->day);
		$this->assertEquals(0, $book->view_count->week);
		$this->assertEquals(0, $book->view_count->month);
		$this->assertEquals(0, $book->view_count->year);
		$this->assertEquals(0, $book->view_count->all);

		$this->get(route('books.show', $book))
			->assertOk();

		$book->refresh();

		$this->assertEquals(1, $book->view_count->day);
		$this->assertEquals(1, $book->view_count->week);
		$this->assertEquals(1, $book->view_count->month);
		$this->assertEquals(1, $book->view_count->year);
		$this->assertEquals(1, $book->view_count->all);

		$this->get(route('books.show', $book))
			->assertOk();

		$book->refresh();

		$this->assertEquals(1, $book->view_count->day);
		$this->assertEquals(1, $book->view_count->week);
		$this->assertEquals(1, $book->view_count->month);
		$this->assertEquals(1, $book->view_count->year);
		$this->assertEquals(1, $book->view_count->all);

		$this->get(route('books.show', $book), ['REMOTE_ADDR' => $this->faker->ipv4])
			->assertOk();

		$book->refresh();

		$this->assertEquals(2, $book->view_count->day);
		$this->assertEquals(2, $book->view_count->week);
		$this->assertEquals(2, $book->view_count->month);
		$this->assertEquals(2, $book->view_count->year);
		$this->assertEquals(2, $book->view_count->all);

		$this->get(route('books.sections.show', ['book' => $book, 'section' => $section->inner_id]), ['REMOTE_ADDR' => $this->faker->ipv4])
			->assertOk();

		$book->refresh();

		$this->assertEquals(3, $book->view_count->day);
		$this->assertEquals(3, $book->view_count->week);
		$this->assertEquals(3, $book->view_count->month);
		$this->assertEquals(3, $book->view_count->year);
		$this->assertEquals(3, $book->view_count->all);
	}

	public function testClearPeriodDay()
	{
		$book_view_count = factory(ViewCount::class)
			->create(['day' => '1',
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

	public function testViewClear()
	{
		$book_view_ip = factory(BookViewIp::class)
			->create();

		$book = $book_view_ip->book;

		$this->assertNotNull(BookViewIp::first());

		Artisan::call('clear:book_view_ip');

		$this->assertNull(BookViewIp::first());
	}

	public function testViewPolicyIfBookAccepted()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create();

		$user = $book->create_user;

		$this->assertTrue($user->can('view', $book));

		$user = factory(User::class)->create();

		$this->assertTrue($user->can('view', $book));
	}

	public function testViewPolicyIfBookPrivate()
	{
		$book = factory(Book::class)
			->states('private')
			->create();

		$user = $book->create_user;

		$this->assertTrue($user->can('view', $book));

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('view', $book));
	}
}
