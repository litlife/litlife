<?php

namespace App\Console\Commands\Refresh;

use App\Book;
use Illuminate\Console\Command;

class RefreshBookGenresHelper extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:book_genres_helper {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Обновляет столбец книги, который служит для более легкого поиска по жанрам у всех книг';

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
		Book::any()->chunkById($this->argument('limit'), function ($items) {
			foreach ($items as $item) {
				$this->item($item);
			}
		});
	}

	public function item($book)
	{
		echo("$book->id \n");

		$book->refreshGenreHelper();
		$book->save();
	}
}
