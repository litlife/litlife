<?php

namespace App\Console\Commands;

use App\BookParse;
use Illuminate\Console\Command;

class BookFindWaitedAndFillDB extends Command
{

	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'book:find_wait_status_and_fill_db {limit=10}';

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

		$book_parses = BookParse::take($limit)
			->waited()
			->oldest()
			->get();

		if ($book_parses->count()) {
			$bar = $this->output->createProgressBar($book_parses->count());

			$bar->setFormatDefinition('custom', ' %current%/%max% -- %message%');
			$bar->setFormat('custom');

			foreach ($book_parses as $book_parse) {

				$bar->setMessage('Обрабатываем книгу ' . $book_parse->book_id);

				$this->call('book:fill_db_from_source', [
					'book_id' => $book_parse->book_id
				]);

				$bar->advance();
			}

			$bar->finish();
		} else {
			$this->info('Ни одна книга не ожидает обработки');
		}
	}
}
