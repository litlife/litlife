<?php

namespace App\Console\Commands;

use App\Author;
use App\Book;
use Illuminate\Console\Command;

class AuthorSetLPStatusForAllBooks extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'author:set_lp_status_for_all_books {author_id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда устанвливает для всех книг автора статус любительского перевода';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$author_id = $this->argument('author_id');

		$author = Author::findOrFail($author_id);

		$author
			->any_books()
			->chunk(100, function ($books) {
				foreach ($books as $book) {
					$this->item($book);
				}
			});
	}

	public function item(Book $book)
	{
		$book->is_lp = true;
		$book->save();
	}
}
