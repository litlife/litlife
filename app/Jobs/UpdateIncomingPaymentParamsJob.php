<?php

namespace App\Jobs;

use App\UserPaymentTransaction;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Litlife\Unitpay\Facades\UnitPay;

class UpdateIncomingPaymentParamsJob
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

			$response = UnitPay::getPayment([
				'paymentId' => $this->payment->payment_id
			])->request();

			$this->payment->params = $response->getParams();

			if ($response->result()->status == 'error') {
				$this->transaction->statusError();
			}

			$this->transaction->save();
			$this->payment->save();
		});
	}
}
