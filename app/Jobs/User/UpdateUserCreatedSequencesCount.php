<?php

namespace App\Jobs\User;

use App\User;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateUserCreatedSequencesCount
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
		$this->user->data->created_sequences_count = $this->user->created_sequences()->count();
		//$this->user->data->ignoreObservableEvents();
		$this->user->data->save();
		//$this->user->data->unignoreObservableEvents();
	}
}
