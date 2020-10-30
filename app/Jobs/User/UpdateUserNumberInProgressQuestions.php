<?php

namespace App\Jobs\User;

use App\User;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateUserNumberInProgressQuestions
{
	use Dispatchable, SerializesModels;

	public $user;

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
	 * @throws
	 */
	public function handle()
	{
		$this->user->flushNumberInProgressQuestions();
	}
}
