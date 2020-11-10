<?php

namespace Tests\Feature\Book\Section;

use App\Author;
use App\Section;
use App\User;
use Tests\TestCase;

class SectionIndexTest extends TestCase
{
	public function testIndexIfBookPrivate()
	{
		$section = Section::factory()->create();

		$book = $section->book;
		$book->statusPrivate();
		$book->save();
		$book->refresh();

		$user = User::factory()->create();

		$this->get(route('books.sections.index', ['book' => $book]))
			->assertForbidden();

		$this->actingAs($user)
			->get(route('books.sections.index', ['book' => $book]))
			->assertForbidden();

		$book->create_user_id = $user->id;
		$book->save();
		$book->refresh();

		$this->actingAs($user)
			->get(route('books.sections.index', ['book' => $book]))
			->assertOk()
			->assertSeeText($section->name);
	}

	public function testIndexIfBookAccept()
	{
		$section = Section::factory()->create();

		$book = $section->book;
		$book->statusAccepted();
		$book->save();
		$book->refresh();

		$user = User::factory()->create();

		$this->get(route('books.sections.index', ['book' => $book]))
			->assertOk()
			->assertSeeText($section->name);

		$this->actingAs($user)
			->get(route('books.sections.index', ['book' => $book]))
			->assertOk()
			->assertSeeText($section->name);
	}

	public function testSectionIndexPurchaseVariableInView()
	{
		$author = Author::factory()->with_author_manager_can_sell()->with_book_for_sale_purchased()->create();

		$manager = $author->managers->first();
		$book = $author->books->first();
		$user = $manager->user;
		$buyer = $book->boughtUsers->first();
		$purchase = $book->purchases->where('buyer_user_id', auth()->id())->first();

		$this->assertTrue($book->isForSale());

		$this->actingAs($buyer)
			->get(route('books.sections.index', $book))
			->assertOk()
			->assertViewHas(['purchase' => $purchase]);

		$this->get(route('books.sections.index', $book))
			->assertOk()
			->assertViewHas(['purchase' => null]);

		$user2 = User::factory()->create();

		$this->actingAs($user2)
			->get(route('books.sections.index', $book))
			->assertOk()
			->assertViewHas(['purchase' => null]);
	}
}
