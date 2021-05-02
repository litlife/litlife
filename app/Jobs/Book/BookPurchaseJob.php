<?php

namespace App\Jobs\Book;

use App\Book;
use App\Notifications\BookPurchasedNotification;
use App\Notifications\BookSoldNotification;
use App\User;
use App\UserPaymentTransaction;
use App\UserPurchase;
use Carbon\Carbon;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class BookPurchaseJob
{
	use Dispatchable;

	protected $book;
	protected $buyer;
	protected $seller;
	protected $buyer_referer;
	protected $seller_referer;
	protected $author_profit_percent;
	protected $comission_from_refrence_buyer = 0;
	protected $comission_from_refrence_seller = 0;

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @param User $buyer
	 * @param User $seller
	 * @return void
	 */
	public function __construct(Book $book, User $buyer, User $seller)
	{
		$this->book = $book;
		$this->buyer = $buyer;
		$this->seller = $seller;

		$this->buyer_referer = $this->buyer->referred_by_user()->first();
		$this->seller_referer = $this->seller->referred_by_user()->first();

		if (!empty($this->buyer_referer))
			$this->comission_from_refrence_buyer = $this->buyer_referer->pivot->comission_buy_book;

		if (!empty($this->seller_referer))
			$this->comission_from_refrence_seller = $this->seller_referer->pivot->comission_sell_book;

		$this->author_profit_percent = $this->book->seller_manager()->profit_percent;
	}

    /**
     * Execute the job.
     *
     * @return void
     * @throws \Throwable
     */
	public function handle()
	{
		DB::transaction(function () {

			if ($this->buyer->balance(true) < $this->book->price)
				throw new \LogicException('Not enough money');

			$purchase = new UserPurchase;
			$purchase->buyer()->associate($this->buyer);
			$purchase->seller()->associate($this->seller);
			$purchase->price = $this->book->price;
			$purchase->site_commission = $this->getSiteComission();
			$this->book->purchases()->save($purchase);

			$buyer_transaction = new UserPaymentTransaction;
			$buyer_transaction->user()->associate($this->buyer);
			$buyer_transaction->sum = -$this->book->price;
			$buyer_transaction->typeBuy();
			$buyer_transaction->statusSuccess();
			$buyer_transaction->balance_before = $this->buyer->balance();
			$purchase->transaction()->save($buyer_transaction);

			$comission_transaction = new UserPaymentTransaction;
			$comission_transaction->user_id = config('app.user_id');
			$comission_transaction->sum = $this->getSiteSum();
			$comission_transaction->typeComission();
			$comission_transaction->statusSuccess();
			$purchase->transaction()->save($comission_transaction);

			$seller_transaction = new UserPaymentTransaction;
			$seller_transaction->user()->associate($this->seller);
			$seller_transaction->sum = $this->getSellerSum();
			$seller_transaction->typeSell();
			$seller_transaction->statusSuccess();
			$seller_transaction->balance_before = $this->seller->balance();
			$purchase->transaction()->save($seller_transaction);

			if (!empty($this->buyer_referer)) {
				$transaction = new UserPaymentTransaction;
				$transaction->user()->associate($this->buyer_referer);
				$transaction->sum = $this->getBuyerRefererSum();
				$transaction->typeComissionRefererBuyer();
				$transaction->statusSuccess();
				$transaction->balance_before = $this->buyer_referer->balance();
				$purchase->transaction()->save($transaction);

				$this->buyer_referer->balance(true);
			}

			if (!empty($this->seller_referer)) {
				$transaction = new UserPaymentTransaction;
				$transaction->user()->associate($this->seller_referer);
				$transaction->sum = $this->getSellerRefererSum();
				$transaction->typeComissionRefererSeller();
				$transaction->statusSuccess();
				$transaction->balance_before = $this->seller_referer->balance();
				$purchase->transaction()->save($transaction);

				$this->seller_referer->balance(true);
			}

			$this->seller->balance(true);
			$this->buyer->balance(true);

			$this->buyer->purchasedBookCountRefresh();
			$this->book->boughtTimesCountRefresh();

            $this->addTimeToDisabledAdsUntilForBuyer(intval(ceil($purchase->price)));

			$this->seller->notify(new BookSoldNotification($purchase));
			$this->buyer->notify(new BookPurchasedNotification($purchase));
		});
	}

	public function getSiteComission()
	{
		$comission = 100 - $this->author_profit_percent;

		if (!empty($this->buyer_referer))
			$comission = $comission - $this->comission_from_refrence_buyer;

		if (!empty($this->seller_referer))
			$comission = $comission - $this->comission_from_refrence_seller;

		if ($comission < 0)
			throw new \LogicException('Negative comission');

		return $comission;
	}

	public function getSiteSum()
	{
		return round(($this->book->price / 100) * (100 - $this->author_profit_percent), 2, PHP_ROUND_HALF_DOWN) - $this->getBuyerRefererSum() - $this->getSellerRefererSum();
	}

	public function getBuyerRefererSum()
	{
		if (!empty($this->buyer_referer))
			return round(($this->book->price / 100) * $this->comission_from_refrence_buyer, 2, PHP_ROUND_HALF_UP);
		else
			return 0;
	}

	public function getSellerRefererSum()
	{
		if (!empty($this->seller_referer))
			return round(($this->book->price / 100) * $this->comission_from_refrence_seller, 2, PHP_ROUND_HALF_UP);
		else
			return 0;
	}

	public function getSellerSum()
	{
		return $this->book->price - $this->getSiteSum() - $this->getBuyerRefererSum() - $this->getSellerRefererSum();
	}

	public function addTimeToDisabledAdsUntilForBuyer(int $days)
    {
        $this->buyer->data->adsDisabledUntilAppendDays($days);
        $this->buyer->data->save();
    }
}
