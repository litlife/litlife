<?php

namespace App\Jobs\User;

use App\User;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateUserCreatedBooksCount
{
	use Dispatchable;

	protected $user;

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 * @return void
	 */
	public function __construct(User $user)
	{
		$this->user = $user;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$this->user->data->created_books_count = $this->user->created_books()
			->anyNotTrashed()
			->count();

		$this->user->data->save();
	}
}
