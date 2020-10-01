<?php

namespace App\Console\Commands\Refresh;

use App\UserEmail;
use Illuminate\Console\Command;

class RefreshAllUserEmailsIsValidColumn extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_user_emails_is_valid';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Проверяет все пользовательские почтовые ящики на валидность';

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
		UserEmail::chunkById(1000, function ($items) {
			foreach ($items as $item) {
				echo('email: ' . $item->id . "\n");

				$item->isValidRefresh();
				$item->save();
			}
		});
	}
}
