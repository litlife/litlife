<?php

namespace App\Jobs\Book;

use App\Book;
use App\BookViewIp;
use App\ViewCount;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class UpdateBookViews
{
	use Dispatchable;

	protected $book;

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @return void
	 */
	public function __construct(Book $book)
	{
		$this->book = $book;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		DB::transaction(function () {

			$view = $this->book->user_view_ips()->where('ip', request()->ip());

			if (!$view->first()) {

				$view = new BookViewIp;
				$view->book_id = $this->book->id;
				$view->ip = request()->ip();
				$view->save();

				$view_count = $this->book->view_count;

				if (empty($view_count))
					$view_count = new ViewCount;

				$view_count->day++;
				$view_count->week++;
				$view_count->month++;
				$view_count->year++;
				$view_count->all++;

				$this->book->view_count()->save($view_count);
			}
		});
	}
}
