<?php

namespace App\Console\Commands\Old;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OldBookGroupToNewAndUpdateCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:book_group_and_update_counters';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Запускает указанные команды';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Artisan::call('to_new:fill_origin_book_id');
		Artisan::call('to_new:book_group');
		Artisan::call('refresh:all_users_counters');
		Artisan::call('refresh:all_books_counters');
	}
}
