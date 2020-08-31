<?php

namespace App\Console\Commands\Payment;

use App\Enums\PaymentStatusEnum;
use App\Enums\TransactionType;
use App\Jobs\OutgoingPaymentJob;
use App\UserOutgoingPayment;
use Illuminate\Console\Command;

class HandleOutgoingWaitedPayments extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'payments:handle_waited_outgoing';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обрабатывает все ожидающие заявки на выплаты';

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
		UserOutgoingPayment::whereHas('transaction', function ($query) {
			$query->where('status', PaymentStatusEnum::Wait)
				->where('type', TransactionType::withdrawal);
		})->chunkById(10, function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	public function item($item)
	{
		OutgoingPaymentJob::dispatch($item);
	}
}
