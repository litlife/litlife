<?php

namespace App\Jobs\Book;

use App\Book;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateBookReadStatusCount
{
	use Dispatchable;

	protected $book;
	protected $status;

	protected $code_columns = [
		'readed' => 'user_read_count',
		'read_later' => 'user_read_later_count',
		'read_now' => 'user_read_now_count',
		'read_not_complete' => 'user_read_not_complete_count',
		'not_read' => 'user_read_not_read_count'
	];

	/**
	 * Create a new job instance.
	 *
	 * @param Book $book
	 * @param $status string
	 * @return void
	 */
	public function __construct(Book $book, $status = null)
	{
		$this->book = $book;
		$this->status = $status;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		if (empty($this->status)) {

			foreach ($this->code_columns as $status => $column) {
				$this->updateStatusUsersCount($status);
			}
		} else {
			if (array_key_exists($this->status, $this->code_columns))
				$this->updateStatusUsersCount($this->status);
		}

		$this->book->save();
	}

	public function updateStatusUsersCount($status)
	{
		$column = $this->code_columns[$status];

		$this->book->$column = $this->book->users_read_statuses()
			->where('status', $status)
			->count();
	}
}
