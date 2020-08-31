<?php

namespace App\Console\Commands;

use App\DatabaseNotification;
use Illuminate\Console\Command;

class DeleteOutdatedNotifications extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'notifications:delete_outdated';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда удаляет устаревшие уведомления';

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
		$days = config('litlife.delete_notifications_in_days');

		DatabaseNotification::where('read_at', '<', now()->subDays($days))
			->delete();
	}
}
