<?php

namespace App\Console\Commands;

use App\PasswordReset;
use Illuminate\Console\Command;

class DeleteExpiredPasswordResets extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'password_resets:delete_expired';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда удаляет устаревшие токены для восстановления пароля';

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
		$days = config('litlife.number_of_days_after_which_to_delete_unused_password_recovery_tokens');

		PasswordReset::notUsed()->where('created_at', '<', now()->subDays($days))
			->delete();
	}
}
