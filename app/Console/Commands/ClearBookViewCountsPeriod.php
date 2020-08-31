<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ClearBookViewCountsPeriod extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'clear:book_view_counts_period {period?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Очищает количество просмотров книги для опеределенного периода';

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
		$period = $this->argument('period');

		if (!in_array($period, ['day', 'week', 'month', 'year'])) {
			$period = $this->choice('Укажите период', ['day', 'week', 'month', 'year']);
		}

		if (Schema::hasColumn('view_counts', $period)) {
			Schema::table('view_counts', function (Blueprint $table) use ($period) {
				$table->dropColumn($period);
			});
		}

		if (!Schema::hasColumn('view_counts', $period)) {
			Schema::table('view_counts', function (Blueprint $table) use ($period) {
				$table->integer($period)->default(0);
			});
		}
	}
}
