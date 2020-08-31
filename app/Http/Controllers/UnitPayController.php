<?php

namespace App\Http\Controllers;

use App\Book;
use App\Notifications\InvoiceWasSuccessfullyPaidNotification;
use App\UserPaymentTransaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Litlife\Unitpay\Facades\UnitPay;

class UnitPayController extends Controller
{
	public function handler(Request $request)
	{
		$method = $request->input('method');
		$params = $request->input('params');

		if ($params['account'] == 'test' or !empty($params['test']))
			return $this->testRequestSuccessResponse();

		DB::beginTransaction();

		try {
			UnitPay::checkIp($request->ip())
				->checkSignature($method, $params);

			$transaction = UserPaymentTransaction::find(intval($params['account']));

			if (empty($transaction))
				throw new Exception(__('user_payment_transaction.transaction_not_found'));

			$payment = $transaction->operable;

			$this->checkPaymentParams($payment, $transaction, $params);

			if ($transaction->isStatusSuccess())
				return $this->successResponse();

			if (empty($payment->payment_id))
				$payment->payment_id = $params['unitpayId'];

			if (!empty($params['paymentType']))
				$payment->payment_type = $params['paymentType'];

			if ($method == 'check') {
				$transaction->statusProcessing();
			} elseif ($method == 'error') {
				$transaction->statusError();

				if (!empty($payment->payment_id)) {
					$response = UnitPay::getPayment(['paymentId' => $payment->payment_id])
						->request();

					$payment->params = $response->getParams();

					if (optional($response->result())->status == 'wait') {
						$transaction->save();
						$payment->save();

						DB::commit();

						return ['error' =>
							[
								'message' => 'Запрос послан типа error, но при получении данных о транзакции получен статус wait',
								'code' => 0
							]
						];
					}
				}
			} elseif ($method == 'pay') {
				$transaction->statusSuccess();

				if (!empty($payment->payment_id)) {
					$response = UnitPay::getPayment(['paymentId' => $payment->payment_id])
						->request();

					$payment->params = $response->getParams();
				}
			}

			$transaction->save();
			$payment->save();

			if ($transaction->isStatusSuccess()) {
				$payment->user->balance(true);
			}

			DB::commit();

			return $this->successResponse();

		} catch (Exception $exception) {
			DB::rollBack();

			report($exception);

			return ['error' =>
				[
					'message' => $exception->getMessage(),
					'code' => $exception->getCode()
				]
			];
		}
	}

	private function testRequestSuccessResponse()
	{
		return ['result' =>
			['message' => 'Тестовый запрос успешно обработан']
		];
	}

	private function checkPaymentParams($payment, $transaction, $params)
	{
		if ($params['account'] != $transaction->id)
			throw new Exception('Account or transaction id did not match');

		if ($params['orderCurrency'] != $payment->currency)
			throw new Exception('Currency did not match');

		if ($params['orderSum'] != $transaction->sum)
			throw new Exception('Order sum did not match');
	}

	private function successResponse()
	{
		return ['result' =>
			['message' => 'Запрос успешно обработан']
		];
	}

	public function depositSuccess(Request $request)
	{
		$this->validate($request, [
			'account' => 'required|string',
			'paymentId' => 'required|integer',
		]);

		DB::beginTransaction();

		$transaction = auth()->user()->payment_transactions()
			->findOrFail($request->account);

		$payment = $transaction->operable;

		$response = UnitPay::getPayment(['paymentId' => $request->paymentId])
			->request();

		$payment->params = $response->getParams();

		if ($payment->params->result->status == 'success') {
			$payment->transaction->statusSuccess();
		}

		$payment->push();

		auth()->user()->balance(true);

		DB::commit();

		if (isset($transaction->params->buy_book)) {
			$book_id = $transaction->params->buy_book;

			$book = Book::findOrFail($book_id);

			return redirect()
				->route('books.buy', ['book' => $book]);
		} else {
			$user = $transaction->user;
			$user->notify(new InvoiceWasSuccessfullyPaidNotification($transaction));
		}

		return redirect()
			->route('users.wallet', ['user' => $transaction->user]);
	}

	public function depositError(Request $request)
	{
		$this->validate($request, [
			'account' => 'required|string',
			'paymentId' => 'required|integer',
		]);

		DB::beginTransaction();

		$transaction = auth()->user()->payment_transactions()
			->findOrFail($request->account);

		$payment = $transaction->operable;

		$response = UnitPay::getPayment(['paymentId' => $request->paymentId])
			->request();

		$payment->params = $response->getParams();

		if ($payment->params->result->status != 'error')
			throw new Exception('Ошибочный статус');

		$transaction->statusError();
		$payment->save();
		$transaction->save();

		DB::commit();

		return redirect()
			->route('users.wallet', ['user' => $transaction->user])
			->withErrors(['errors' => __('user_incoming_payment.error')]);
	}
}
