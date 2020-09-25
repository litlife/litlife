<?php

namespace Tests\Feature\Book\Section;

use App\Author;
use App\Book;
use App\Section;
use App\User;
use App\UserPurchase;
use Tests\TestCase;

class SectionShowPolicyTest extends TestCase
{
	public function testViewPolicyIfAllBookPaid()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 0]);

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$section2 = factory(Section::class)
			->create(['book_id' => $book->id]);

		$reader = factory(User::class)
			->create();

		$this->assertFalse($reader->can('view', $section));
		$this->assertFalse($reader->can('view', $section2));
	}

	public function testViewPolicyIfTwoFirstSectionsFree()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 2]);

		$free_section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$free_section2 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$free_section2->insertAfterNode($free_section);

		$section3 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$section3->insertAfterNode($free_section2);

		$section4 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$section4->insertAfterNode($section3);

		$reader = factory(User::class)
			->create();

		$this->assertTrue($reader->can('view', $free_section));
		$this->assertTrue($reader->can('view', $free_section2));
		$this->assertFalse($reader->can('view', $section3));
		$this->assertFalse($reader->can('view', $section4));
	}

	public function testViewPolicyIfTwoFirstSectionsFreeAndDescendants()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 2]);

		$free_section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$free_section2 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$free_section->appendNode($free_section2);

		$section3 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$free_section2->appendNode($section3);

		$section4 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$section3->appendNode($section4);

		$reader = factory(User::class)
			->create();

		$this->assertTrue($reader->can('view', $free_section));
		$this->assertTrue($reader->can('view', $free_section2));
		$this->assertFalse($reader->can('view', $section3));
		$this->assertFalse($reader->can('view', $section4));
	}

	public function testPageViewPolicyIfFirstPageFreeAndUserBuyABook()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 0]);

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$section2 = factory(Section::class)
			->create(['book_id' => $book->id]);

		$reader = factory(User::class)
			->create();

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $reader->id,
				'purchasable_type' => 'book',
				'purchasable_id' => $book->id,
			]);

		$this->assertTrue($reader->can('view', $section));
		$this->assertTrue($reader->can('view', $section2));
	}

	public function testPageViewPolicyIfSectionPaidAndUserAAuthor()
	{
		$author = factory(Author::class)
			->states('with_author_manager', 'with_book_for_sale')
			->create();

		$writer = $author->managers->first()->user;
		$book = $author->books->first();

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$this->assertTrue($book->isForSale());

		$this->assertTrue($writer->can('view', $section));
	}

	public function testPageViewPolicyIfSectionPaidAndUserGuest()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 1]);

		$free_section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$section2 = factory(Section::class)
			->create(['book_id' => $book->id]);
		$free_section->appendNode($section2);

		$this->assertTrue((new User)->can('view', $free_section));
		$this->assertFalse((new User)->can('view', $section2));
	}

	public function testViewPolicyIfBookReadAccessClosed()
	{
		$book = factory(Book::class)
			->create(['price' => 100, 'free_sections_count' => 1]);
		$book->readAccessDisable();
		$book->save();

		$section = factory(Section::class)
			->create(['book_id' => $book->id]);

		$user = factory(User::class)
			->create();

		$this->assertFalse($user->can('view', $section));
	}

	public function testViewPolicyIfBookPurchasedAndReadAccessClosed()
	{
		$book = factory(Book::class)
			->states('with_section')
			->create(['price' => 100, 'free_sections_count' => 1]);
		$book->readAccessDisable();
		$book->push();
		$book->refresh();

		$section = $book->sections()->first();

		$reader = factory(User::class)
			->create();

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $reader->id,
				'purchasable_type' => 'book',
				'purchasable_id' => $section->book->id,
			]);

		$this->assertFalse($reader->can('view', $section));
	}

}
