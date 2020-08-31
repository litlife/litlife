<?php

namespace App\Console\Commands\Old;

use App\Book;
use Illuminate\Console\Command;

class OldAllBookFillDBFromSource extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'book_fill_db:all {limit=10}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Ищет все книги со статусом ожидания обработки и из источника заполняет базу данных';

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
		$limit = $this->argument('limit');

		$count = Book::any()->where('parse', 'wait')->count();

		if ($count > 0) {
			$c = (int)ceil($count / $limit);

			for ($i = 0; $i <= $c; $i++) {

				$skip = ($i * $limit);

				$books = Book::any()
					->where('parse', 'wait')
					->orderBy('id', 'asc')
					->get();

				foreach ($books as $book) {

					$this->info('Книга ' . $book->id);

					$this->book($book);
				}
			}
		} else {
			$this->info('Ни одна книга не ожидает обработки');
		}
	}

	public function book($book)
	{
		// если не найден источник файла, то пропускаем
		if (empty($book->source))
			return false;

		// если книга не ожидает обработки, тоже пропускаем
		if ($book->parse != 'wait')
			return false;

		$this->call('book:fill_db_from_source', [
			'book_id' => $book->id
		]);
	}
}
