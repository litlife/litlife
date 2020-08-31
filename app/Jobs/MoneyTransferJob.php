<?php

namespace App\Jobs;

use App\User;
use App\UserMoneyTransfer;
use App\UserPaymentTransaction;
use Exception;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class MoneyTransferJob
{
	use Dispatchable, SerializesModels;

	private $sender;
	private $recepient;
	private $sum;

	/**
	 * Create a new job instance.
	 *
	 * @param User $sender
	 * @param User $recepient
	 * @param int $sum
	 * @return void
	 */
	public function __construct(User $sender, User $recepient, int $sum)
	{
		$this->sender = $sender;
		$this->recepient = $recepient;
		$this->sum = $sum;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			if ($this->sender->balance(true) < $this->sum)
				throw new Exception('Not enoung money');

			$transfer = new UserMoneyTransfer;
			$transfer->sender()->associate($this->sender);
			$transfer->recepient()->associate($this->recepient);
			$transfer->save();

			$sender_transaction = new UserPaymentTransaction;
			$sender_transaction->user()->associate($this->sender);
			$sender_transaction->sum = -$this->sum;
			$sender_transaction->typeTransfer();
			$sender_transaction->statusSuccess();
			$sender_transaction->balance_before = $this->sender->balance();
			$transfer->transaction()->save($sender_transaction);

			$recepient_transaction = new UserPaymentTransaction;
			$recepient_transaction->user()->associate($this->recepient);
			$recepient_transaction->sum = $this->sum;
			$recepient_transaction->typeReceipt();
			$recepient_transaction->statusSuccess();
			$sender_transaction->balance_before = $this->recepient->balance();
			$transfer->transaction()->save($recepient_transaction);

			$this->recepient->balance(true);
			$this->sender->balance(true);
		});
	}
}
