<?php

namespace App\Traits;

use App\Enums\CacheTags;
use App\Enums\PaymentStatusEnum;
use App\Enums\TransactionType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

trait Payments
{
	public function balanceRefresh()
	{
		$this->balance(true);
	}

	public function balance($fresh = false)
	{
		if (!empty($fresh)) {
			$balance = $this->payment_transactions()
				->whereIn('status', [PaymentStatusEnum::Success])
				->sum('sum');

			$this->balance = $balance - $this->frozen_balance(true);
			$this->save();
		}

		return $this->balance;
	}

	public function payment_transactions()
	{
		return $this->hasMany('App\UserPaymentTransaction', 'user_id');
	}

	public function frozen_balance($fresh = false)
	{
		if ($fresh)
			$this->flushCachedFrozenBalance();

		return Cache::tags([CacheTags::FrozenBalance])->remember($this->id, 86400, function () {

			$frozen_balance = abs($this->payment_transactions()
				->where(function ($query) {
					$query->where(function ($query) {
						$query->where('type', TransactionType::withdrawal)
							->whereNotIn('status', [PaymentStatusEnum::Success, PaymentStatusEnum::Canceled]);
					})->orWhere(function ($query) {
						$query->where('type', TransactionType::deposit)
							->whereNotIn('status', [PaymentStatusEnum::Success, PaymentStatusEnum::Canceled, PaymentStatusEnum::Wait, PaymentStatusEnum::Processing, PaymentStatusEnum::Error]);
					});
				})
				->sum('sum'));

			return $frozen_balance;
		});
	}

	public function flushCachedFrozenBalance()
	{
		Cache::tags([CacheTags::FrozenBalance])->pull($this->id);
	}

	public function setBalanceAttribute($value)
	{
		$this->attributes['balance'] = number_format($value, 2, '.', '');
	}

	public function getBalanceAttribute($value)
	{
		return $value;
	}

	public function withdraw_balance()
	{
		return $this->payment_transactions()
			->where(function ($query) {

				$query->where(function ($query) {
					$query->whereIn('type', [TransactionType::buy, TransactionType::sell,
						TransactionType::comission, TransactionType::receipt,
						TransactionType::transfer, TransactionType::comission_referer_buyer,
						TransactionType::comission_referer_seller])
						->where('status', PaymentStatusEnum::Success);
				})->orWhere(function ($query) {
					$query->where('type', TransactionType::withdrawal)
						->whereIn('status', [PaymentStatusEnum::Success, PaymentStatusEnum::Processing]);
				});
			})
			->sum('sum');
	}

	public function incoming_payment()
	{
		return $this->hasMany('App\UserIncomingPayment', 'user_id');
	}

	public function outgoing_payment()
	{
		return $this->hasMany('App\UserOutgoingPayment', 'user_id');
	}

	public function sales()
	{
		return $this->hasMany('App\UserPurchase', 'seller_user_id');
	}

	public function purchases()
	{
		return $this->hasMany('App\UserPurchase', 'buyer_user_id');
	}

	public function purchased_books()
	{
		return $this->morphedByMany('App\Book', 'purchasable', 'user_purchases', 'buyer_user_id');
	}

	public function sold_books()
	{
		return $this->morphedByMany('App\Book', 'purchasable', 'user_purchases', 'seller_user_id');
	}

	public function wallets()
	{
		return $this->hasMany('App\UserPaymentDetail', 'user_id');
	}

	public function getFilledWallets()
	{
		return $this->wallets;
	}

	public function transfers()
	{
		return $this->hasMany('App\UserMoneyTransfer', 'sender_user_id');
	}

	public function receiving()
	{
		return $this->hasMany('App\UserMoneyTransfer', 'recepient_user_id');
	}

	public function today_profit()
	{
		return $this->payment_transactions()
			->whereIn('status', [PaymentStatusEnum::Success])
			->where('created_at', '>', Carbon::now()->subDay())
			->sum('sum');
	}

	public function month_profit()
	{
		return $this->payment_transactions()
			->whereIn('status', [PaymentStatusEnum::Success])
			->where('created_at', '>', Carbon::now()->subMonth())
			->sum('sum');
	}
}