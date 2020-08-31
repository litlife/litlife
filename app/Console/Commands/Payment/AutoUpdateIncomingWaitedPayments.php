<?php

namespace App\Console\Commands\Payment;

use App\UserPaymentTransaction;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Artisan;

class AutoUpdateIncomingWaitedPayments extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'payments:handle_waited_incoming {limit=100} {latest_transaction_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обновляет параметры и статус для ожидающих платежей';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$latest_transaction_id = $this->argument('latest_transaction_id');

		UserPaymentTransaction::processed()
			->deposit()
			->whereHasMorph(
				'operable',
				'App\UserIncomingPayment',
				function (Builder $query) {
					$query->unitPay()
						->whereNotNull('payment_id');
				}
			)
			->where('status_changed_at', '<', now()->subMinutes(10))
			->where('id', '>=', $latest_transaction_id)
			->with('operable')
			->chunk(100, function ($transactions) {
				foreach ($transactions as $transaction) {
					$this->handleTransaction($transaction);
				}
			});
	}

	public function handleTransaction(UserPaymentTransaction $transaction)
	{
		if (!$transaction->isDeposit())
			return false;

		if (!$transaction->isStatusProcessing())
			return false;

		$payment = $transaction->operable;

		if (empty($payment->payment_id))
			return false;

		Artisan::call('payments:incoming_payment_update', ['transaction_id' => $transaction->id]);
	}
}
