<?php

namespace App\Console\Commands;

use App\BookParse;
use Illuminate\Console\Command;

class BookParseAllWaited extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'book:parse_all_waited {last_book_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Ищет файлы книги которые ожидают обработки и обрабатывает';
	protected $bar;

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
		$lastBookId = $this->argument('last_book_id');

		$query = BookParse::waited()
			->where('book_id', '>=', $lastBookId)
			->with('book')
			->oldest();

		$count = $query->count();

		if ($count < 1)
			$this->info('Ни одна книга не ожидает обработки');
		else {
			$this->bar = $this->output->createProgressBar($count);

			$this->bar->setFormatDefinition('custom', ' %current%/%max% -- %message%');
			$this->bar->setFormat('custom');

			$query->chunkById(10, function ($bookParses) {
				foreach ($bookParses as $bookParse) {
					$this->bar->setMessage('Обрабатываем книгу ' . $bookParse->book_id);
					$this->book($bookParse->book);
					$this->bar->advance();
				}
			});

			$this->bar->finish();
		}
	}

	public function book($book)
	{
		if ($book->parse->isWait()) {
			try {
				$this->call('book:fill_db_from_source', [
					'book_id' => $book->id
				]);
			} catch (\Exception $exception) {
				$this->warn($exception->getMessage());
			}
		}
	}
}