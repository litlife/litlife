<?php

namespace App\Jobs\User;

use App\Enums\UserRelationType;
use App\User;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateUserSubscriptionsCount
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
		$this->user->subscriptions_count = $this->user->relationship()->where('status', UserRelationType::Subscriber)->count();
		//$this->user->ignoreObservableEvents();
		$this->user->save();
		//$this->user->unignoreObservableEvents();
	}
}
