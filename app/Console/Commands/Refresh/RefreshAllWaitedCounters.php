<?php

namespace App\Console\Commands\Refresh;

use App\Events\BookFilesCountChanged;
use App\Jobs\User\UpdateUserReadStatusCount;
use App\User;
use Illuminate\Console\Command;

class RefreshAllWaitedCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_waited_counters {limit=1000} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет все ожидающие счетчики';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		User::any()
			->where('refresh_counters', true)
			->where('id', '>=', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('user: ' . $item->id);

					UpdateUserReadStatusCount::dispatch($item);
				}
			});
	}
}
