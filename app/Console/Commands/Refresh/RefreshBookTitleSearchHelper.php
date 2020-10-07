<?php

namespace App\Console\Commands\Refresh;

use App\Book;
use Illuminate\Console\Command;

class RefreshBookTitleSearchHelper extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:book_title_search_helper {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обновляет строку с заголовком книги и полным именем писателей';

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
		Book::any()
			->with('authors')
			->chunkById($this->argument('limit'), function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	public function item(Book $item)
	{
		echo("Book $item->id \n");

		$item->updateTitleAuthorsHelper();
		$item->save();
	}
}
