<?php

namespace App\Console\Commands\Refresh;

use App\Enums\PaymentSystemType;
use App\Enums\TransactionType;
use App\PaymentSystemComission;
use Exception;
use Illuminate\Console\Command;
use Litlife\Unitpay\Facades\UnitPay;

class RefreshUnitPayComissions extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:unitpay_comissions';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обновляет данные по комиссиям в базе данных';

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
		$result = UnitPay::getCommissions()
			->request();

		if ($result->isError())
			throw new Exception($result->getErrorMessage(), $result->getErrorCode());

		foreach ($result->result() as $paymentSystem => $comission) {
			if (PaymentSystemType::hasKey($paymentSystem)) {
				PaymentSystemComission::updateOrCreate([
					'payment_aggregator' => 'unitpay',
					'payment_system_type' => PaymentSystemType::getValue($paymentSystem),
					'transaction_type' => TransactionType::deposit
				], [
						'comission' => $comission
					]
				);
			}
		}
	}
}
