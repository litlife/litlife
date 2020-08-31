<?php

namespace App\Console\Commands\Payment;

use App\Jobs\UpdateIncomingPaymentParamsJob;
use App\UserPaymentTransaction;
use Illuminate\Console\Command;

class IncomingPaymentUpdate extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'payments:incoming_payment_update {transaction_id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обновляет данные пополнения счета';

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
		$id = $this->argument('transaction_id');

		$transaction = UserPaymentTransaction::find($id);

		if (empty($transaction)) {
			$this->error('Транзакция с указанным ID: ' . $id . ' не найдена');
			return false;
		}

		UpdateIncomingPaymentParamsJob::dispatch($transaction);

		$this->info('Данные транзакции успешно обновлены');
	}
}
