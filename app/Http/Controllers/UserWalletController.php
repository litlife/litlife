<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserOutgoingPayment;
use App\Http\Requests\StoreUserPaymentDetails;
use App\Jobs\MoneyTransferJob;
use App\Notifications\BillingInformationChangedNotification;
use App\Notifications\WithdrawalOrderedNotification;
use App\User;
use App\UserIncomingPayment;
use App\UserMoneyTransfer;
use App\UserOutgoingPayment;
use App\UserPaymentDetail;
use App\UserPaymentTransaction;
use App\UserPurchase;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Litlife\Unitpay\Facades\UnitPay;

class UserWalletController extends Controller
{
	/**
	 * Вывод платежных данных пользователя
	 *
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function paymentDetails(User $user)
	{
		$this->authorize('update_billing_information', $user);

		return view('user.wallet.index', compact('user'));
	}

	/**
	 * Сохранение платежных данных
	 *
	 * @param StoreUserPaymentDetails $request
	 * @param User $user
	 * @return Response
	 * @throws
	 */
	public function paymentDetailsSave(StoreUserPaymentDetails $request, User $user)
	{
		$this->authorize('update_billing_information', $user);

		$wallets = $user->wallets;

		foreach ($request->all() as $type => $number) {
			if (in_array($type, ['card', 'qiwi', 'wmr', 'yandex'])) {
				if ($type == 'card')
					$number = (int)filter_var($number, FILTER_SANITIZE_NUMBER_INT);

				if ($number != optional($wallets->where('type', $type)->first())->number) {
					if ($wallet = $wallets->where('type', $type)->first()) {
						$user->wallets()
							->where('type', $type)
							->delete();

						$changed = true;
					}

					if (!empty($number)) {
						$wallet = new UserPaymentDetail();
						$wallet->type = $type;
						$wallet->number = $number;
						if ($wallet->isCard())
							$wallet->updateCardInfo();
						$user->wallets()->save($wallet);

						$changed = true;
					}
				}
			}
		}

		if (!empty($changed))
			$user->notify(new BillingInformationChangedNotification($user));

		return redirect()
			->route('users.wallet.payment_details', $user)
			->with('success', __('user_payment_detail.saved_successfully'));
	}

	public function wallet(User $user)
	{
		$this->authorize('wallet', $user);

		$transactions = $user->payment_transactions()
			->with(['operable', 'user'])
			->latest()
			->simplePaginate();

		$transactions->loadMorph('operable', [
			UserPurchase::class => [
				'purchasable' => function ($query) {
					$query->withTrashed();
				}
			],
			UserMoneyTransfer::class => ['recepient']
		]);

		return view('user.wallet.wallet', [
			'user' => $user,
			'transactions' => $transactions
		]);
	}

	public function deposit(User $user)
	{
		$this->authorize('wallet', $user);

		return view('user.wallet.deposit', ['user' => $user]);
	}

	public function depositPay(Request $request, User $user)
	{
		$this->authorize('wallet', $user);

		$this->validate($request, [
			'sum' => 'required|numeric',
			'payment_type' => 'required|in:' . implode(',', config('unitpay.allowed_payment_types')) . ''
		], [], __('user_incoming_payment'));

		$sum = $request->sum;
		$type = $request->payment_type;

		DB::beginTransaction();

		$payment = new UserIncomingPayment;
		$payment->payment_type = $type;
		$payment->user_id = $user->id;
		$payment->ip = request()->ip();
		$payment->currency = 'RUB';
		$payment->payment_aggregator = 'unitpay';
		$payment->params = [];
		$payment->save();

		$transaction = new UserPaymentTransaction;
		$transaction->user_id = $user->id;
		$transaction->sum = $sum;
		$transaction->statusWait();
		$transaction->typeDeposit();

		$payment->transaction()->save($transaction);

		DB::commit();

		$params['sum'] = $sum;
		$params['account'] = $transaction->id;
		$params['desc'] = __('user_incoming_payment.desc', ['user_id' => $user->name, 'sum' => $params['sum']]);
		$params['currency'] = 'RUB';
		$params['backUrl'] = route('users.wallet', ['user' => $user]);

		$url = UnitPay::getFormUrl($type, $params);

		return redirect()->away($url);
	}

	public function orderWithdrawal(User $user)
	{
		$this->authorize('withdrawal', $user);

		$outgoing_payment = $user->outgoing_payment()
			->simplePaginate();

		return view('user.wallet.withdrawal', [
			'user' => $user,
			'outgoing_payment' => $outgoing_payment
		]);
	}

	public function saveWithdrawal(StoreUserOutgoingPayment $request, User $user)
	{
		$this->authorize('withdrawal', $user);

		$wallet = $user->wallets()
			->where('id', intval($request->wallet))
			->first();

		$user->balance(true);

		if (empty($wallet))
			return redirect()
				->route('users.wallet.withdrawal', $user)
				->withInput($request->all())
				->withErrors(['wallet' => __('user_outgoing_payment.wallet_not_found')]);

		if ($request->sum > $user->balance)
			return redirect()
				->route('users.wallet.withdrawal', $user)
				->withInput($request->all())
				->withErrors(['sum' => __('user_outgoing_payment.sum_cannot_exceed_the_available_balance')]);

		if ($request->sum < config('litlife.min_outgoing_payment_sum'))
			return redirect()
				->route('users.wallet.withdrawal', $user)
				->withInput($request->all())
				->withErrors(['sum' => __('user_outgoing_payment.sum_can_not_be_less_than_the_minimum')]);

		DB::beginTransaction();

		$payment = new UserOutgoingPayment;
		$payment->ip = $request->ip();
		$payment->purse = $wallet->number;
		$payment->payment_type = $wallet->type;
		$payment->wallet_id = $wallet->id;
		$user->outgoing_payment()->save($payment);

		$transaction = new UserPaymentTransaction;
		$transaction->user()->associate($user);
		$transaction->sum = -$request->sum;
		$transaction->typeWithdrawal();
		$transaction->statusWait();
		$payment->transaction()->save($transaction);

		$user->balance(true);

		$transaction->user->notify(new WithdrawalOrderedNotification($payment));

		DB::commit();

		return redirect()
			->route('users.wallet', $user)
			->with('success', __('user_outgoing_payment.payment_created'));
	}

	public function payWaitedTransaction(User $user, $transaction)
	{
		$transaction = $user->payment_transactions()
			->findOrFail($transaction);

		$this->authorize('pay', $transaction);

		$params['sum'] = $transaction->sum;
		$params['type'] = $transaction->operable->payment_type;
		$params['account'] = $transaction->id;
		$params['desc'] = __('user_incoming_payment.desc', ['user_id' => $user->name, 'sum' => $params['sum']]);
		$params['currency'] = 'RUB';
		$params['backUrl'] = route('users.wallet', ['user' => $user]);

		if (empty($params['type']))
			$params['type'] = 'card';

		$url = UnitPay::getFormUrl($params['type'], $params);

		return redirect()->away($url);
	}

	public function transactionCancel(User $user, $transaction)
	{
		$transaction = $user->payment_transactions()
			->wait()
			->findOrFail($transaction);

		$this->authorize('cancel', $transaction);

		DB::transaction(function () use ($user, $transaction) {

			$transaction->statusCanceled();
			$transaction->save();

			$user->balance(true);
		});

		return redirect()
			->route('users.wallet', $user);
	}

	public function orderTransfer(User $user)
	{
		$this->authorize('transfer_money', $user);

		return view('user.wallet.transfer', [
			'user' => $user
		]);
	}

	public function saveTransfer(Request $request, User $sender)
	{
		$this->authorize('transfer_money', $sender);

		$this->validate($request, [
			'recepient_id' => 'required|integer',
			'sum' => 'required|numeric'
		], [], __('user_money_transfer'));

		$recepient = User::find($request->recepient_id);

		if ($sender->id == $recepient->id)
			return redirect()
				->route('users.wallet.transfer', ['user' => $sender])
				->withErrors(['recepient_id' => __('user_money_transfer.the_recipient_id_must_not_match_the_sender_id')]);

		if (empty($recepient))
			return redirect()
				->route('users.wallet.transfer', ['user' => $sender])
				->withErrors(['recepient_id' => __('user_money_transfer.recepient_not_found')]);

		if ($request->sum > $sender->balance())
			return redirect()
				->route('users.wallet.transfer', ['user' => $sender])
				->withErrors(['sum' => __('user_money_transfer.sum_cannot_exceed_the_available_balance')]);

		dispatch(new MoneyTransferJob($sender, $recepient, $request->sum));

		return redirect()
			->route('users.wallet', ['user' => $sender])
			->with(['success' => __('user_money_transfer.transfer_to_user_success', ['sum' => $request->sum, 'user_name' => $recepient->userName])]);
	}
}
