<?php

namespace App\Console\Commands\Old;

use App\BookGroup;
use App\Jobs\Book\BookGroupJob;
use Illuminate\Console\Command;

class OldBooksConnetToNew extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:book_group {limit=500} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = '';

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
		$latest_id = $this->argument('latest_id');

		BookGroup::where('id', '>=', $latest_id)
			->chunkById($limit, function ($groups) {
				foreach ($groups as $group) {
					$this->item($group);
				}
			});
	}

	function item(BookGroup $group)
	{
		echo($group->id . "\n");

		$main_book = $group->main_book;

		$books = $group->not_main_books()->any()->get();

		if (empty($main_book)) {
			if ($books->count() > 1) {
				$books = $books->sortBy('vote_average');
				$main_book = $books->pop();
			}
		}

		if (empty($main_book->main_book_id)) {
			if (!empty($main_book)) {
				foreach ($books->all() as $book) {
					BookGroupJob::dispatch($main_book, $book, false, false, false);
				}
			}
		}
	}
}
