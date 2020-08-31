<?php

namespace App\Console\Commands\Refresh;

use App\Author;
use App\Events\BookFilesCountChanged;
use Illuminate\Console\Command;

class RefreshAllAuthorsCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_authors_counters {limit=1000} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет счетчики всех авторов';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Author::any()
			->where('id', '>', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					echo('author: ' . $item->id . "\n");

					$this->call('refresh:author_counters', [
						'id' => $item->id
					]);
				}
			});
	}
}
