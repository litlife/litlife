<?php

namespace Tests\Feature\Artisan;

use App\Book;
use App\BookGroup;
use App\Jobs\Book\BookGroupJob;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class OldBooksConnetToNewTest extends TestCase
{
	public function testInit()
	{
		$group = BookGroup::factory()->with_main_book()->create();

		$mainBook = $group->books()->first();

		$minorBook = Book::factory()->create();
		$minorBook->addToGroup($group);
		$minorBook->save();

		Artisan::call('to_new:book_group', ['latest_id' => $group->id]);

		$mainBook->refresh();
		$minorBook->refresh();

		$this->assertTrue($mainBook->isInGroup());
		$this->assertTrue($minorBook->isInGroup());
		$this->assertTrue($mainBook->isMainInGroup());
	}

	public function testMainBookNotFound()
	{
		$group = BookGroup::factory()->with_main_book()->create();

		$mainBook = $group->books()->first();

		$minorBook = Book::factory()->create();
		$minorBook->addToGroup($group);
		$minorBook->save();

		$minorBook2 = Book::factory()->create(['vote_average' => 10]);
		$minorBook2->addToGroup($group);
		$minorBook2->save();

		$mainBook->forceDelete();

		Artisan::call('to_new:book_group', ['latest_id' => $group->id]);

		$minorBook->refresh();
		$minorBook2->refresh();

		$this->assertTrue($minorBook->isInGroup());
		$this->assertTrue($minorBook2->isMainInGroup());
		$this->assertTrue($minorBook->isAttachedToBook($minorBook2));
	}

	public function testMainBookSoftDeleted()
	{
		$group = BookGroup::factory()->with_main_book()->create();

		$mainBook = $group->books()->first();

		$minorBook = Book::factory()->create(['vote_average' => 10]);
		$minorBook->addToGroup($group);
		$minorBook->save();

		$minorBook2 = Book::factory()->create();
		$minorBook2->addToGroup($group);
		$minorBook2->save();

		$mainBook->delete();

		Artisan::call('to_new:book_group', ['latest_id' => $group->id]);

		$minorBook->refresh();
		$minorBook2->refresh();

		$this->assertTrue($minorBook->isMainInGroup());
		$this->assertTrue($minorBook2->isInGroup());
		$this->assertTrue($minorBook2->isAttachedToBook($minorBook));
	}

	public function testOneBookInGroup()
	{
		$group = BookGroup::factory()->create();

		$minorBook = Book::factory()->create(['vote_average' => 10]);
		$minorBook->addToGroup($group);
		$minorBook->save();

		Artisan::call('to_new:book_group', ['latest_id' => $group->id]);

		$minorBook->refresh();

		$this->assertFalse($minorBook->isMainInGroup());
	}

	public function testIfMinorBookIsMainBook()
	{
		$group = BookGroup::factory()->create();

		$mainBook = Book::factory()->create();
		$mainBook->addToGroup($group);
		$mainBook->save();

		$minorBook = Book::factory()->create();
		$minorBook->addToGroup($group);
		$minorBook->save();

		$minorBook2 = Book::factory()->create();
		$minorBook2->addToGroup($group, true);
		$minorBook2->save();

		BookGroupJob::dispatch($mainBook, $minorBook);
		BookGroupJob::dispatch($mainBook, $minorBook2);

		Artisan::call('to_new:book_group', ['latest_id' => $group->id]);

		$mainBook->refresh();
		$minorBook->refresh();
		$minorBook2->refresh();

		$this->assertTrue($mainBook->isMainInGroup());
		$this->assertTrue($minorBook->isNotMainInGroup());
		$this->assertTrue($minorBook2->isNotMainInGroup());

		$this->assertEquals($mainBook->id, $minorBook->main_book_id);
		$this->assertEquals($mainBook->id, $minorBook2->main_book_id);
	}
}
