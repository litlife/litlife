<?php

namespace App\Console\Commands\Refresh;

use App\Book;
use App\Events\BookFilesCountChanged;
use App\Jobs\Book\BookUpdatePageNumbersJob;
use Illuminate\Console\Command;

class RefreshAllBooksPageNumbers extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_books_page_numbers {limit=500} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет номера страниц для всех книг в обновленном онлайн чтении';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		Book::any()
			->onlineReadNewFormat()
			->where('id', '>', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {
					$this->info('book: ' . $item->id);

					BookUpdatePageNumbersJob::dispatch($item);
				}
			});
	}
}
