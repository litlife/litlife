<?php

namespace Tests\Feature\User\Purchase;

use App\Notifications\BookSaleCanceledNotification;
use App\UserPurchase;
use Litlife\Unitpay\UnitPayFake;
use Tests\TestCase;

class BookSaleCanceledNotificationTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testToArray()
	{
		$purchase = UserPurchase::factory()->book()->create();

		$user = $purchase->seller;
		$book = $purchase->purchasable;

		$notification = new BookSaleCanceledNotification($purchase);

		$array = $notification->toArray($user);

		$this->assertEquals(__('notification.sale_canceled.subject'), $array['title']);

		$this->assertEquals(__('notification.sale_canceled.line', [
			'book_title' => $purchase->purchasable->title,
			'writers_names' => optional($purchase->purchasable->writers()->first())->name
		]), $array['description']);

		$this->assertEquals(route('users.wallet', ['user' => $user]), $array['url']);
	}

	public function testVia()
	{
		$purchase = UserPurchase::factory()->book()->create();

		$user = $purchase->seller;
		$book = $purchase->purchasable;

		$notification = new BookSaleCanceledNotification($purchase);

		$this->assertEquals(['database'], $notification->via($user));
	}
}
