<?php

namespace App\Jobs;

use App\Participation;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateParticipationCounters
{
	use Dispatchable;

	protected $participation;

	/**
	 * Create a new job instance.
	 *
	 * @param Participation $participation
	 * @return void
	 */
	public function __construct(Participation $participation)
	{
		$this->participation = $participation;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 * @throws
	 */
	public function handle()
	{
		$this->participation->updateNewMessagesCount();
		$this->participation->updateLatestMessage();
		$this->participation->save();
	}
}
