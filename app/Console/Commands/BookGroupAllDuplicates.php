<?php

namespace App\Console\Commands;

use App\Author;
use App\Jobs\Book\BookGroupJob;
use Illuminate\Console\Command;

class BookGroupAllDuplicates extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'book:group_all_duplicates {last_author_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Ищет книги у авторов с одинаковым названием и группирует их';
	protected $bar;
	protected $count;

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
		$lastAuthorId = $this->argument('last_author_id');

		$query = Author::accepted()
			->where('id', '>=', $lastAuthorId);

		$count = $query->count();

		if ($count < 1)
			$this->info('Ни один автор не найден');
		else {

			$bar = $this->output->createProgressBar($count);
			$bar->start();

			$query->with([
				'books' => function ($query) {
					$query->notConnected()
						->accepted()
						->orderByRatingDesc();
				}])
				->chunkById(100, function ($authors) use ($bar) {
					foreach ($authors as $author) {
						$this->author($author);
						$bar->advance();
					}
				});

			$bar->finish();
		}
	}

	protected function author($author)
	{
		$array = [];

		foreach ($author->books as $book) {
			$title = trim(mb_strtolower($book->title));
			$array[$title][] = $book;
		}

		foreach (collect($array) as $books) {
			$this->books($books);
		}
	}

	protected function books($books)
	{
		$books = collect($books);

		if ($books->count() > 1) {

			$mainBook = $books->slice(0, 1)->first();

			$books = $books->slice(1);

			foreach ($books as $n => $book) {
				$this->info('Book grouped: ' . route('books.show', $book));
				BookGroupJob::dispatch($mainBook, $book);
			}
		}
	}
}