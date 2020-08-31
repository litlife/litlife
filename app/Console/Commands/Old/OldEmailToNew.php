<?php

namespace App\Console\Commands\Old;

use App\User;
use App\UserEmail;
use Illuminate\Console\Command;

class OldEmailToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:user_email {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда переносит почтовые адреса из таблицы users в таблицу user_emails';

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
		$limit = $this->argument('limit');

		User::any()->chunk(1000, function ($users) {

			foreach ($users as $user) {

				echo($user->id . "\n");

				$user_email = new UserEmail;
				$user_email->email = $user->email;
				$user_email->user_id = $user->id;

				if ($user->ec) {
					$user_email->confirm = true;
				} else {
					$user_email->confirm = false;
				}

				$user_email->notice = true;
				$user_email->rescue = true;

				if ($user->hide_email) {
					$user_email->show_in_profile = false;
				} else {
					$user_email->show_in_profile = true;
				}

				$user_email->save();
			}
		});
	}


}
