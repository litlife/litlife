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
		$this->participation->new_messages_count = $this->participation->conversation
			->messages()
			->notDeletedForUser($this->participation->user_id)
			->where('id', '>', $this->participation->latest_seen_message_id ?? 0)
			->count();

		$latest_message = $this->participation->conversation
			->messages()
			->notDeletedForUser($this->participation->user_id)
			->orderBy('id', 'desc')
			->limit(1)
			->first();

		if (!empty($latest_message))
			$this->participation->latest_message_id = $latest_message->id;
		else
			$this->participation->latest_message_id = null;

		$this->participation->save();
	}
}
