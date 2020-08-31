<?php

namespace Tests\Feature\User\Purchase;

use App\Jobs\Book\BookPurchaseCancelJob;
use App\User;
use App\UserPurchase;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class UserCancelPurchaseTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testCancelSuccessful()
	{
		$user = factory(User::class)->create();
		$user->group->view_financial_statistics = true;
		$user->push();

		$purchase = factory(UserPurchase::class)
			->states('book')
			->create()
			->fresh();

		Bus::fake();

		$this->actingAs($user)
			->get(route('purchases.cancel', $purchase))
			->assertRedirect()
			->assertSessionHas(['success' => __('user_purchases.purchase_successfully_canceled')]);

		Bus::assertDispatched(BookPurchaseCancelJob::class, function ($job) use ($purchase) {
			return $job->userPurchase->id === $purchase->id;
		});
	}
}
