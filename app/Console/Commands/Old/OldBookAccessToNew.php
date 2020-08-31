<?php

namespace App\Console\Commands\Old;

use App\Book;
use Illuminate\Console\Command;

class OldBookAccessToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:book_access {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$limit = $this->argument('limit');

		$count = Book::any()->count();

		$c = (int)ceil($count / $limit);

		for ($i = 0; $i <= $c; $i++) {

			$skip = ($i * $limit);

			$books = Book::any()->select('id', 'secret_hide')
				->take($limit)
				->skip($skip)
				->orderBy('id', 'desc')
				->get();

			foreach ($books as $book) {

				echo($book->id . "\n");

				$this->book($book);
			}
		}
	}

	function book($book)
	{
		if ($book->secret_hide > 0) {
			$book->readAccessDisable();
			$book->downloadAccessDisable();
			$book->save();
		}
	}
}
