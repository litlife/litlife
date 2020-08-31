<?php

namespace App\Jobs\User;

use App\User;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateUserReadStatusCount
{
	use Dispatchable;

	protected $user;
	protected $status;

	protected $code_columns = [
		'readed' => 'book_read_count',
		'read_later' => 'book_read_later_count',
		'read_now' => 'book_read_now_count',
		'read_not_complete' => 'book_read_not_complete_count',
		'not_read' => 'book_read_not_read_count'
	];

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 * @return void
	 */
	public function __construct(User $user, $status = null)
	{
		$this->user = $user;
		$this->status = $status;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		if (!empty($this->status))
			$this->status($this->status);
		else {
			foreach ($this->code_columns as $status => $columnName) {
				$this->status($status);
			}
		}

		$this->user->save();
	}

	public function status($status)
	{
		if (array_key_exists($status, $this->code_columns)) {

			$column = $this->code_columns[$status];

			$this->user->$column = $this->user->books_read_statuses()
				->any()
				->wherePivot('status', $status)
				->count();
		}
	}
}
