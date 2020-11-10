<?php

namespace Tests\Feature\User\Purchase;

use App\Author;
use App\Book;
use App\Jobs\Book\BookPurchaseCancelJob;
use App\Jobs\Book\BookPurchaseJob;
use App\Notifications\BookPurchaseCanceledNotification;
use App\Notifications\BookSaleCanceledNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Litlife\Unitpay\UnitPayFake;
use Tests\TestCase;

class UserBookPurchaseCancelTest extends TestCase
{
	public function testCancelSuccessful()
	{
		$buyer_referer = factory(User::class)
			->states('with_100_balance')
			->create();

		$seller_referer = factory(User::class)
			->states('with_200_balance')
			->create();

		$buyer = factory(User::class)
			->states('with_300_balance')
			->create();
		$buyer->setReferredByUserId($buyer_referer->id);

		$author = Author::factory()->with_author_manager_can_sell()->create();

		$manager = $author->managers()->first();

		$seller = factory(User::class)
			->states('with_400_balance')
			->create();
		$seller->setReferredByUserId($seller_referer->id);
		$manager->user_id = $seller->id;
		$manager->save();

		$book = Book::factory()->on_sale()->create();
		$book->price = 65;
		$book->save();

		$author->written_books()->sync([$book->id]);

		$book->refresh();

		BookPurchaseJob::dispatch($book, $buyer, $seller);

		Notification::fake();

		$purchase = $buyer->purchases()->first();
		$purchase->refresh();
		$buyer->refresh();
		$seller->refresh();
		$buyer_referer->refresh();
		$seller_referer->refresh();

		$this->assertEquals(1, $buyer->data->books_purchased_count);
		$this->assertEquals(65, $book->price);
		$this->assertEquals(235.00, $buyer->balance(true));
		$this->assertEquals(445.50, $seller->balance(true));
		$this->assertEquals(106.50, $buyer_referer->balance(true));
		$this->assertEquals(206.50, $seller_referer->balance(true));

		BookPurchaseCancelJob::dispatch($purchase);

		$purchase->refresh();

		$this->assertTrue($purchase->isCanceled());

		$this->assertTrue($purchase->buyer_transaction->isStatusCanceled());
		$this->assertTrue($purchase->seller_transaction->isStatusCanceled());
		$this->assertTrue($purchase->commission_transaction->isStatusCanceled());
		$this->assertTrue($purchase->referer_buyer_transaction->isStatusCanceled());
		$this->assertTrue($purchase->referer_seller_transaction->isStatusCanceled());

		$buyer->refresh();
		$seller->refresh();
		$buyer_referer->refresh();
		$seller_referer->refresh();

		$this->assertEquals(300, $buyer->balance());
		$this->assertEquals(400, $seller->balance());
		$this->assertEquals(100, $buyer_referer->balance());
		$this->assertEquals(200, $seller_referer->balance());
		$this->assertEquals(0, $buyer->data->books_purchased_count);

		Notification::assertSentTo($buyer, BookPurchaseCanceledNotification::class);
		Notification::assertSentTo($seller, BookSaleCanceledNotification::class);
	}
}
