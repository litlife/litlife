<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use App\UserPurchase;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class BookDisplayAdsPolicyTest extends TestCase
{
	public function testDisplayAdsForPurchasedBooksPolicy()
	{
		$user = factory(User::class)
			->create();

		$book = factory(Book::class)
			->create();

		$this->assertTrue($user->can('display_ads', $book));

		$this->assertTrue((new User)->can('display_ads', $book));

		$purchase = factory(UserPurchase::class)
			->create([
				'buyer_user_id' => $user->id,
				'purchasable_type' => 'book',
				'purchasable_id' => $book->id
			]);

		$book->refresh();

		$this->assertFalse($user->can('display_ads', $book));
	}

	public function testDontDisplayAdsIfBookOnSale()
	{
		$book = factory(Book::class)
			->states('on_sale', 'with_section')
			->create();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('display_ads', $book));
		$this->assertFalse(Gate::forUser(new User)->allows('display_ads', $book));
		$this->assertFalse(Gate::allows('display_ads', $book));

		$chapter = $book->sections()->chapter()->first();

		$page = $chapter->pages()->first();

		$this->assertFalse($user->can('display_ads', $page));
		$this->assertFalse(Gate::forUser(new User)->allows('display_ads', $page));
		$this->assertFalse(Gate::allows('display_ads', $page));
	}

}
