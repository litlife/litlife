<?php

namespace App\Console\Commands\BookFile;

use App\BookFile;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeletingOldBookFiles extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'bookfiles:deleteting_old {days_ago=7}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Очищает с диска устаревшие файлы книг. Можно указать сколько дней прошло со дня удаления. Значение по умолчанию 7 дней';

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
		$days_ago = $this->argument('days_ago');

		BookFile::onlyTrashed()
			->where('deleted_at', '<', Carbon::now()->subDays($days_ago)->toDateTimeString())
			->orderBy('deleted_at', 'asc')
			->limit(1000)
			->forceDelete();
	}
}
