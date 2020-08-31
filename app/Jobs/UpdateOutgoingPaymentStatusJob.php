<?php

namespace App\Jobs;

use App\UserPaymentTransaction;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Litlife\Unitpay\Facades\UnitPay;

class UpdateOutgoingPaymentStatusJob
{
	use Dispatchable, SerializesModels;

	private $payment;
	private $transaction;

	/**
	 * Create a new job instance.
	 *
	 * @param UserPaymentTransaction $transaction
	 * @return void
	 */
	public function __construct(UserPaymentTransaction $transaction)
	{
		$this->transaction = $transaction;
		$this->payment = $this->transaction->operable;
		$this->payment->setRelation('transaction', $this->transaction);
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			$response = UnitPay::massPaymentStatus([
				'transactionId' => $this->payment->uniqid
			])->request();

			$this->payment->params = $response->getParams();
			$this->payment->payment_aggregator = 'unitpay';

			if ($response->isError()) {
				$this->transaction->statusError();
			} elseif ($response->isSuccess()) {
				if ($response->result()->status == 'success') {
					$this->transaction->statusSuccess();
				}
			}

			$this->transaction->save();
			$this->payment->save();

		});
	}
}
