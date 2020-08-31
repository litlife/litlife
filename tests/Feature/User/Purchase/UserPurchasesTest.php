<?php

namespace Tests\Feature\User\Purchase;

use App\Author;
use App\Notifications\BookPurchasedNotification;
use App\Notifications\BookSoldNotification;
use App\ReferredUser;
use App\User;
use App\UserPurchase;
use Illuminate\Support\Facades\Notification;
use Litlife\Unitpay\Facades\UnitPay;
use Litlife\Unitpay\UnitPayFake;
use Tests\TestCase;

class UserPurchasesTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testIsBook()
	{
		$purchase = factory(UserPurchase::class)
			->states('book')
			->create(['price' => 110]);

		$this->assertTrue($purchase->isBook());
	}

	public function testPurchaseBook()
	{
		Notification::fake();

		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$book = $author->books->first();
		$book->price = rand(50, 200) . '.' . rand(0, 99);
		$book->save();

		$comission_sum = round(($book->price / 100) * config('litlife.comission'), 2, PHP_ROUND_HALF_DOWN);
		$buyer_sum = $book->price;
		$seller_sum = $book->price - $comission_sum;

		$this->assertEquals($book->price, $seller_sum + $comission_sum);

		$seller = $author->seller();

		$buyer = factory(User::class)
			->states('with_thousand_money_on_balance')
			->create();

		$this->assertEquals(1000, $buyer->balance);

		$this->actingAs($buyer)
			->get(route('books.buy', $book))
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.show', $book));

		$purchase = $buyer->purchases()->first();
		$seller = $book->seller();
		$book->refresh();

		$this->assertNotNull($purchase);
		$this->assertEquals($buyer->id, $purchase->buyer->id);
		$this->assertEquals($book->seller()->id, $purchase->seller->id);
		$this->assertEquals($book->price, $purchase->price);
		$this->assertEquals($purchase->site_commission, config('litlife.comission'));
		$this->assertEquals('book', $purchase->purchasable_type);
		$this->assertEquals($book->id, $purchase->purchasable_id);

		$this->assertEquals($buyer->id, $purchase->buyer_transaction->user_id);
		$this->assertEquals(-$buyer_sum, $purchase->buyer_transaction->sum);

		$this->assertEquals($seller->id, $purchase->seller_transaction->user_id);
		$this->assertEquals($seller_sum, $purchase->seller_transaction->sum);

		$this->assertEquals(config('app.user_id'), $purchase->commission_transaction->user_id);
		$this->assertEquals($comission_sum, $purchase->commission_transaction->sum);

		$this->assertEquals($seller_sum, $seller->balance);
		$this->assertEquals(1000 - $book->price, $buyer->balance);

		$this->assertEquals(1, $buyer->data->books_purchased_count);
		$this->assertEquals(1, $book->bought_times_count);

		Notification::assertSentTo(
			$seller,
			BookSoldNotification::class,
			function ($notification, $channels) use ($purchase) {
				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$mail = $notification->toMail($purchase->seller);

				$this->assertEquals(__('notification.book_sold.subject'), $mail->subject);

				$this->assertEquals(__('notification.book_sold.line', [
					'sum' => $purchase->seller_transaction->sum,
					'user_name' => $purchase->buyer->userName,
					'book_title' => $purchase->purchasable->title
				]), $mail->introLines[0]);

				$this->assertEquals(__('notification.book_sold.action'), $mail->actionText);

				$this->assertEquals(route('users.wallet', ['user' => $purchase->seller]), $mail->actionUrl);

				return $notification->user_purchase->id == $purchase->id;
			}
		);

		Notification::assertSentTo(
			$buyer,
			BookPurchasedNotification::class,
			function ($notification, $channels) use ($purchase) {
				$this->assertContains('mail', $channels);
				$this->assertContains('database', $channels);

				$mail = $notification->toMail($purchase->buyer);

				$this->assertEquals(__('notification.book_purchased.subject'), $mail->subject);

				$this->assertEquals(__('notification.book_purchased.line', [
					'book_title' => $purchase->purchasable->title,
					'writers_names' => implode(', ', $purchase->purchasable->writers->pluck('name')->toArray())
				]), $mail->introLines[0]);

				$this->assertEquals(__('notification.book_purchased.action'), $mail->actionText);

				$this->assertEquals(route('books.show', ['book' => $purchase->purchasable]), $mail->actionUrl);

				return $notification->user_purchase->id == $purchase->id;
			}
		);
	}

	public function testBuyDeposit()
	{
		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$book = $author->books->first();
		$book->price = 100;
		$book->save();

		$seller = $author->seller();

		$buyer = factory(User::class)
			->states('with_thousand_money_on_balance')
			->create();

		$response = $this->actingAs($buyer)
			->post(route('books.buy.deposit', $book),
				[
					'payment_type' => 'card'
				]);

		$payment = $buyer->incoming_payment()->orderBy('id', 'desc')
			->get()->first();

		$params['sum'] = $book->price;
		$params['account'] = $payment->transaction->id;
		$params['desc'] = __('user_incoming_payment.desc_buy_book', ['title' => $book->getSellTitle(), 'sum' => $params['sum']]);
		$params['currency'] = 'RUB';
		$params['backUrl'] = route('books.show', ['book' => $book]);

		$redirect_url = UnitPay::getFormUrl('card', $params);

		$response->assertRedirect($redirect_url);
	}

	public function testTryBuyAgain()
	{
		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$book = $author->books->first();
		$book->price = 100;
		$book->save();

		$seller = $author->seller();

		$buyer = factory(User::class)
			->states('with_thousand_money_on_balance')
			->create();

		$this->assertEquals(1000, $buyer->balance);

		$this->actingAs($buyer)
			->get(route('books.buy', $book))
			->assertRedirect(route('books.show', $book));

		$purchase = $buyer->purchases->first();
		$seller = $book->seller();

		$this->actingAs($buyer)
			->get(route('books.buy', $book))
			->assertForbidden();
	}

	public function testUsersListBoughtHttp()
	{
		$purchase = factory(UserPurchase::class)
			->states('book')
			->create();

		$this->actingAs($purchase->seller)
			->get(route('books.users.bought', $purchase->purchasable))
			->assertOk()
			->assertSeeText($purchase->buyer->userName);
	}

	public function testDontSeeUserIfPurchaseCanceled()
	{
		$purchase = factory(UserPurchase::class)
			->states('book', 'canceled')
			->create();

		$this->actingAs($purchase->seller)
			->get(route('books.users.bought', $purchase->purchasable))
			->assertOk()
			->assertDontSeeText($purchase->buyer->userName);
	}

	public function testPurchaseWithBuyerReference()
	{
		Notification::fake();

		$comission_from_reference_buyer = rand(1, 9);
		$comission_from_reference_seller = rand(1, 9);

		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$book = $author->books->first();
		$book->price = rand(50, 200) . '.' . rand(0, 99);
		$book->save();

		$manager = $author->managers->first();
		$manager->profit_percent = rand(30, 60);
		$manager->save();

		$referer_buyer_sum = round(($book->price / 100) * $comission_from_reference_buyer, 2, PHP_ROUND_HALF_UP);
		$comission_sum = round(($book->price / 100) * (100 - $manager->profit_percent), 2, PHP_ROUND_HALF_DOWN) - $referer_buyer_sum;

		$buyer_sum = $book->price;

		$seller_sum = $book->price - $comission_sum - $referer_buyer_sum;

		$this->assertEquals($book->price, $seller_sum + $comission_sum + $referer_buyer_sum);

		$seller = $author->seller();

		$buyer = factory(User::class)
			->states('with_thousand_money_on_balance')
			->create();

		$reference = factory(ReferredUser::class)
			->create([
				'comission_buy_book' => $comission_from_reference_buyer,
				'comission_sell_book' => $comission_from_reference_seller,
				'referred_user_id' => $buyer->id
			])
			->fresh();

		$referer = $reference->referred_by_user;

		$this->assertNotEquals($referer->id, $buyer->id);

		$this->assertEquals(1000, $buyer->balance);

		$this->actingAs($buyer)
			->get(route('books.buy', $book))
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.show', $book));

		$purchase = $buyer->purchases()->first();
		$seller = $book->seller();
		$book->refresh();

		$this->assertNotNull($purchase);
		$this->assertEquals($buyer->id, $purchase->buyer->id);
		$this->assertEquals($book->seller()->id, $purchase->seller->id);
		$this->assertEquals($book->price, $purchase->price);
		$this->assertEquals($purchase->site_commission, (100 - $manager->profit_percent) - $comission_from_reference_buyer);
		$this->assertEquals('book', $purchase->purchasable_type);
		$this->assertEquals($book->id, $purchase->purchasable_id);

		$this->assertEquals($buyer->id, $purchase->buyer_transaction->user_id);
		$this->assertEquals(-$buyer_sum, $purchase->buyer_transaction->sum);

		$this->assertEquals($seller->id, $purchase->seller_transaction->user_id);
		$this->assertEquals($seller_sum, $purchase->seller_transaction->sum);

		$this->assertEquals($referer->id, $purchase->referer_buyer_transaction->user_id);
		$this->assertEquals($referer_buyer_sum, $purchase->referer_buyer_transaction->sum);

		$this->assertEquals(config('app.user_id'), $purchase->commission_transaction->user_id);
		$this->assertEquals($comission_sum, $purchase->commission_transaction->sum);

		$this->assertEquals($seller_sum, $seller->balance);
		$this->assertEquals(1000 - $book->price, $buyer->balance);

		$this->assertEquals(1, $buyer->data->books_purchased_count);
		$this->assertEquals(1, $book->bought_times_count);
	}

	public function testPurchaseWithSellerReference()
	{
		Notification::fake();

		$comission_from_reference_buyer = rand(1, 9);
		$comission_from_reference_seller = rand(1, 9);

		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$book = $author->books->first();
		$book->price = rand(50, 200) . '.' . rand(0, 99);
		$book->save();

		$manager = $author->managers->first();
		$manager->profit_percent = rand(30, 60);
		$manager->save();

		$referer_seller_sum = round(($book->price / 100) * $comission_from_reference_seller, 2, PHP_ROUND_HALF_UP);
		$comission_sum = round(($book->price / 100) * (100 - $manager->profit_percent), 2, PHP_ROUND_HALF_DOWN) - $referer_seller_sum;

		$buyer_sum = $book->price;

		$seller_sum = $book->price - $comission_sum - $referer_seller_sum;

		$this->assertEquals($book->price, $seller_sum + $comission_sum + $referer_seller_sum);

		$seller = $author->seller();

		$reference = factory(ReferredUser::class)
			->create([
				'comission_buy_book' => $comission_from_reference_buyer,
				'comission_sell_book' => $comission_from_reference_seller,
				'referred_user_id' => $seller->id
			])
			->fresh();

		$referer = $reference->referred_by_user;

		$buyer = factory(User::class)
			->states('with_thousand_money_on_balance')
			->create();

		$this->assertEquals(1000, $buyer->balance);

		$this->actingAs($buyer)
			->get(route('books.buy', $book))
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.show', $book));

		$purchase = $buyer->purchases()->first();
		$seller = $book->seller();
		$book->refresh();

		$this->assertNotNull($purchase);
		$this->assertEquals($buyer->id, $purchase->buyer->id);
		$this->assertEquals($book->seller()->id, $purchase->seller->id);
		$this->assertEquals($book->price, $purchase->price);
		$this->assertEquals($purchase->site_commission, (100 - $manager->profit_percent) - $comission_from_reference_seller);
		$this->assertEquals('book', $purchase->purchasable_type);
		$this->assertEquals($book->id, $purchase->purchasable_id);

		$this->assertEquals($buyer->id, $purchase->buyer_transaction->user_id);
		$this->assertEquals(-$buyer_sum, $purchase->buyer_transaction->sum);

		$this->assertEquals($seller->id, $purchase->seller_transaction->user_id);
		$this->assertEquals($seller_sum, $purchase->seller_transaction->sum);

		$this->assertEquals($referer->id, $purchase->referer_seller_transaction->user_id);
		$this->assertEquals($referer_seller_sum, $purchase->referer_seller_transaction->sum);

		$this->assertEquals(config('app.user_id'), $purchase->commission_transaction->user_id);
		$this->assertEquals($comission_sum, $purchase->commission_transaction->sum);

		$this->assertEquals($seller_sum, $seller->balance);
		$this->assertEquals(1000 - $book->price, $buyer->balance);

		$this->assertEquals(1, $buyer->data->books_purchased_count);
		$this->assertEquals(1, $book->bought_times_count);
	}

	public function testPurchaseWithSellerReferenceAndBuyerReference()
	{
		Notification::fake();

		$comission_from_reference_buyer = rand(1, 9);
		$comission_from_reference_seller = rand(1, 9);

		$author = factory(Author::class)
			->states('with_book_for_sale', 'with_author_manager_can_sell')
			->create();

		$book = $author->books->first();
		$book->price = rand(50, 200) . '.' . rand(0, 99);
		$book->save();

		$manager = $author->managers->first();
		$manager->profit_percent = rand(30, 60);
		$manager->save();

		$referer_buyer_sum = round(($book->price / 100) * $comission_from_reference_buyer, 2, PHP_ROUND_HALF_UP);
		$referer_seller_sum = round(($book->price / 100) * $comission_from_reference_seller, 2, PHP_ROUND_HALF_UP);
		$comission_sum = round(($book->price / 100) * (100 - $manager->profit_percent), 2, PHP_ROUND_HALF_DOWN) - $referer_seller_sum - $referer_buyer_sum;

		$buyer_sum = $book->price;

		$seller_sum = $book->price - $comission_sum - $referer_seller_sum - $referer_buyer_sum;

		$this->assertEquals($book->price, $seller_sum + $comission_sum + $referer_seller_sum + $referer_buyer_sum);

		$seller = $author->seller();

		$reference = factory(ReferredUser::class)
			->create([
				'comission_buy_book' => $comission_from_reference_buyer,
				'comission_sell_book' => $comission_from_reference_seller,
				'referred_user_id' => $seller->id
			])
			->fresh();

		$seller_referer = $reference->referred_by_user;

		$buyer = factory(User::class)
			->states('with_thousand_money_on_balance')
			->create();

		$this->assertEquals(1000, $buyer->balance);

		$reference = factory(ReferredUser::class)
			->create([
				'comission_buy_book' => $comission_from_reference_buyer,
				'comission_sell_book' => $comission_from_reference_seller,
				'referred_user_id' => $buyer->id
			])
			->fresh();

		$buyer_referer = $reference->referred_by_user;

		$this->actingAs($buyer)
			->get(route('books.buy', $book))
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.show', $book));

		$purchase = $buyer->purchases()->first();
		$seller = $book->seller();
		$book->refresh();

		$this->assertNotNull($purchase);
		$this->assertEquals($buyer->id, $purchase->buyer->id);
		$this->assertEquals($book->seller()->id, $purchase->seller->id);
		$this->assertEquals($book->price, $purchase->price);
		$this->assertEquals($purchase->site_commission, 100 - $manager->profit_percent - $comission_from_reference_seller - $comission_from_reference_buyer);
		$this->assertEquals('book', $purchase->purchasable_type);
		$this->assertEquals($book->id, $purchase->purchasable_id);

		$this->assertEquals($buyer->id, $purchase->buyer_transaction->user_id);
		$this->assertEquals(-$buyer_sum, $purchase->buyer_transaction->sum);

		$this->assertEquals($seller->id, $purchase->seller_transaction->user_id);
		$this->assertEquals($seller_sum, $purchase->seller_transaction->sum);

		$this->assertEquals($seller_referer->id, $purchase->referer_seller_transaction->user_id);
		$this->assertEquals($referer_seller_sum, $purchase->referer_seller_transaction->sum);

		$this->assertEquals($buyer_referer->id, $purchase->referer_buyer_transaction->user_id);
		$this->assertEquals($referer_buyer_sum, $purchase->referer_buyer_transaction->sum);

		$this->assertEquals(config('app.user_id'), $purchase->commission_transaction->user_id);
		$this->assertEquals($comission_sum, $purchase->commission_transaction->sum);

		$this->assertEquals($seller_sum, $seller->balance);
		$this->assertEquals(1000 - $book->price, $buyer->balance);

		$this->assertEquals(1, $buyer->data->books_purchased_count);
		$this->assertEquals(1, $book->bought_times_count);
	}
}
