<?php

namespace App\Jobs\User;

use App\User;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateUserFavoriteBooksCount
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
		$this->user->user_lib_book_count = $this->user->books()->any()->count();
		//$this->user->ignoreObservableEvents();
		$this->user->save();
		//$this->user->unignoreObservableEvents();
	}
}
