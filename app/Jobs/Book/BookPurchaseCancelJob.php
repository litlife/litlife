<?php

namespace App\Jobs\Book;

use App\Notifications\BookPurchaseCanceledNotification;
use App\Notifications\BookSaleCanceledNotification;
use App\UserPurchase;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BookPurchaseCancelJob
{
	use Dispatchable;

	public $userPurchase;
	private $buyer;
	private $seller;
	private $buyer_referer;
	private $seller_referer;

	/**
	 * Create a new job instance.
	 *
	 * @param UserPurchase $userPurchase
	 * @return void
	 */
	public function __construct(UserPurchase $userPurchase)
	{
		$this->userPurchase = $userPurchase;

		$this->buyer = $this->userPurchase->buyer;
		$this->seller = $this->userPurchase->seller;

		if (!empty($this->buyer))
			$this->buyer_referer = $this->buyer->referred_by_user()->first();

		if (!empty($this->seller))
			$this->seller_referer = $this->seller->referred_by_user()->first();
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			if (!empty($this->userPurchase->buyer_transaction))
				$this->userPurchase->buyer_transaction->statusCanceled();

			if (!empty($this->userPurchase->seller_transaction))
				$this->userPurchase->seller_transaction->statusCanceled();

			if (!empty($this->userPurchase->commission_transaction))
				$this->userPurchase->commission_transaction->statusCanceled();

			if (!empty($this->userPurchase->referer_buyer_transaction))
				$this->userPurchase->referer_buyer_transaction->statusCanceled();

			if (!empty($this->userPurchase->referer_seller_transaction))
				$this->userPurchase->referer_seller_transaction->statusCanceled();

			$this->userPurchase->cancel();
			$this->userPurchase->push();

			if (!empty($this->seller))
				$this->seller->balance(true);

			if (!empty($this->buyer))
				$this->buyer->balance(true);

			if (!empty($this->buyer_referer))
				$this->buyer_referer->balance(true);

			if (!empty($this->seller_referer))
				$this->seller_referer->balance(true);

			if ($this->userPurchase->isBook()) {
				$this->buyer->purchasedBookCountRefresh();
				$this->userPurchase->purchasable->boughtTimesCountRefresh();

				if (!empty($this->buyer))
					$this->buyer->notify(new BookPurchaseCanceledNotification($this->userPurchase));

				if (!empty($this->seller))
					$this->seller->notify(new BookSaleCanceledNotification($this->userPurchase));
			}
		});
	}
}
