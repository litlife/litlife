<?php

namespace App\Http\Controllers;

use App\Jobs\Book\BookPurchaseCancelJob;
use App\UserPurchase;
use Illuminate\Http\Response;

class UserPurchaseController extends Controller
{
	/**
	 * Отмена покупки
	 *
	 * @param UserPurchase $purchase
	 * @return Response
	 * @throws
	 */
	public function cancel(UserPurchase $purchase)
	{
		$this->authorize('cancel', $purchase);

		BookPurchaseCancelJob::dispatch($purchase);

		return redirect()
			->back()
			->with(['success' => __('user_purchases.purchase_successfully_canceled')]);
	}
}
