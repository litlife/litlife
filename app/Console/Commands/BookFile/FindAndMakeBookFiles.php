<?php

namespace App\Console\Commands\BookFile;

use App\Book;
use Illuminate\Console\Command;

class FindAndMakeBookFiles extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'bookfiles:findmake {count=100}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Проверяет есть ли отредактированные тексты книг и создает для них файлы книг';

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
		$count = $this->argument('count');


		$books = Book::anyNotTrashed()
			->waitedNeedCreateNewBookFiles()
			->whereNeedCreateNewBookFilesCooldownIsOver()
			->oldestUserUpdated()
			->get();

		if (!$books->count()) {
			$this->info('Ни для одной книги не нужно создавать файлы');
		} else {

			foreach ($books as $book) {
				$this->call('bookfiles:make', [
					'bookId' => $book->id
				]);
			}
		}
	}
}
