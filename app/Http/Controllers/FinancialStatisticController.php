<?php

namespace App\Http\Controllers;

use App\Jobs\CreateSiteAccountIfNotExists;
use App\User;
use App\UserMoneyTransfer;
use App\UserPaymentTransaction;
use App\UserPurchase;
use Litlife\Unitpay\Facades\UnitPay;

class FinancialStatisticController extends Controller
{
	public function index()
	{
		$this->authorize('view_financial_statistics', User::class);

		$user = User::find(config('app.user_id'));

		if (empty($user)) {
			$this->dispatch(new CreateSiteAccountIfNotExists());

			$user = User::find(config('app.user_id'));
		}

		$request = UnitPay::getPartner()->request();

		$users_sum_balances = User::where('id', '!=', config('app.user_id'))
			->sum('balance');

		$transactions = $user->payment_transactions()
			->with('operable')
			->latest()
			->simplePaginate();

		$transactions->loadMorph('operable', [
			UserPurchase::class => ['purchasable'],
			UserMoneyTransfer::class => ['recepient']
		]);

		$all_waited_withdrawal_sum = abs(UserPaymentTransaction::withdrawal()
			->wait()
			->sum('sum'));

		return view('financial_statistics.index', [
			'user' => $user,
			'request' => $request,
			'users_sum_balances' => $users_sum_balances,
			'all_waited_withdrawal_sum' => $all_waited_withdrawal_sum,
			'transactions' => $transactions
		]);
	}

	public function allTransactionHisory()
	{
		$this->authorize('view_financial_statistics', User::class);

		$transactions = UserPaymentTransaction::latest()
			->with(['operable', 'user'])
			->simplePaginate();

		$transactions->loadMorph('operable', [
			UserPurchase::class => ['purchasable'],
			UserMoneyTransfer::class => ['recepient']
		]);

		return view('financial_statistics.all_transactions', [
			'transactions' => $transactions
		]);
	}

	public function purchases()
	{
		$this->authorize('view_financial_statistics', User::class);

		$purchases = UserPurchase::latest()
			->with(['purchasable', 'seller', 'buyer'])
			->simplePaginate();

		return view('financial_statistics.purchases', [
			'purchases' => $purchases
		]);
	}
}
