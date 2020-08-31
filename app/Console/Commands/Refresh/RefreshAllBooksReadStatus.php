<?php

namespace App\Console\Commands\Refresh;

use App\Book;
use App\Events\BookFilesCountChanged;
use App\Jobs\Book\UpdateBookReadStatusCount;
use Illuminate\Console\Command;

class RefreshAllBooksReadStatus extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_books_read_status {limit=500} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет количество прочитавших и прочие статусы для главных изданий';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Book::any()
			->notConnected()
			->where('id', '>', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('book: ' . $item->id);

					UpdateBookReadStatusCount::dispatch($item);
				}
			});
	}
}
