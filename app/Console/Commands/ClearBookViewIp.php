<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ClearBookViewIp extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'clear:book_view_ip';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Очищает все ip с которых были просмотрены книги';

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
		DB::transaction(function () {
			DB::statement('LOCK TABLE "book_view_ips" IN ACCESS EXCLUSIVE MODE;');
			DB::table('book_view_ips')->truncate();
		});
	}
}
