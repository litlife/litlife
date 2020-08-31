<?php

namespace App\Jobs;

use App\Notifications\WithdrawalSuccessNotification;
use App\UserOutgoingPayment;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;
use Litlife\Unitpay\Facades\UnitPay;

class OutgoingPaymentJob
{
	use Dispatchable, SerializesModels;

	private $transaction;
	private $payment;

	/**
	 * Create a new job instance.
	 *
	 * @param UserOutgoingPayment $payment
	 * @return void
	 */
	public function __construct(UserOutgoingPayment $payment)
	{
		$this->payment = $payment;
		$this->transaction = $payment->transaction;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			if (!$this->transaction->isStatusWait() and !$this->transaction->isStatusError() and !$this->transaction->isStatusProcessing())
				throw new InvalidArgumentException;

			$params = [
				'sum' => abs($this->transaction->sum),
				'purse' => $this->payment->purse,
				'transactionId' => $this->payment->uniqid,
				'paymentType' => $this->payment->payment_type
			];

			$response = UnitPay::massPayment($params)
				->request();

			$this->payment->params = $response->getParams();
			$this->payment->payment_aggregator = 'unitpay';

			if ($response->isError()) {
				$this->error();
			} elseif ($response->isSuccess()) {
				$this->payment->payment_aggregator_transaction_id = $response->result()->payoutId;

				if ($response->result()->status == 'success') {
					$this->success();
				} elseif ($response->result()->status == 'not_completed') {
					$this->processing();
				}
			}

			$this->transaction->save();
			$this->payment->save();

			if ($this->transaction->isStatusSuccess() or $this->transaction->isStatusCanceled()) {
				$this->transaction->user->balance(true);
			}
		});
	}

	public function error()
	{
		$this->transaction->statusError();
		$this->payment->retry_failed_count = $this->payment->retry_failed_count + 1;
		$this->payment->last_failed_retry_at = now();

		if ($this->payment->retry_failed_count > config('litlife.max_outgoing_payment_retry_failed_count'))
			$this->transaction->statusCanceled();
	}

	public function success()
	{
		$this->transaction->statusSuccess();
		$this->transaction->user->notify(new WithdrawalSuccessNotification($this->payment));
	}

	public function processing()
	{
		$this->transaction->statusProcessing();
	}
}
