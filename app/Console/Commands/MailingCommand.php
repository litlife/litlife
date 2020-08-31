<?php

namespace App\Console\Commands;

use App\Mailing;
use App\Notifications\InvitationToSellBooksNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class MailingCommand extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'mailing:invitation_to_sell_books {limit=1} {latest_id=0}';

	protected $description = 'Отправляет приглашения продвать книги на указанные в рассылке адреса в соответствии с приоритетом';

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
		$mailings = Mailing::waited()
			->when(!empty($this->argument('latest_id')), function ($query) {
				$query->where('id', '>=', $this->argument('latest_id'));
			})
			->orderBy('priority', 'desc')
			->limit($this->argument('limit'))
			->get();

		foreach ($mailings as $mailing) {
			$this->mailing($mailing);
		}
	}

	public function mailing($mailing)
	{
		Notification::route('mail', $mailing->email)
			->notify(new InvitationToSellBooksNotification());

		$mailing->sent_at = now();
		$mailing->save();
	}
}
