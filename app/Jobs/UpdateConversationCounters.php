<?php

namespace App\Jobs;

use App\Conversation;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateConversationCounters
{
	use Dispatchable, SerializesModels;

	protected $conversation;

	/**
	 * Create a new job instance.
	 *
	 * @param Conversation $conversation
	 * @return void
	 */
	public function __construct(Conversation $conversation)
	{
		$this->conversation = $conversation;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 * @throws
	 */
	public function handle()
	{
		$this->conversation->latest_message_id = $this->conversation->messages()->latestWithId()->first()->id;
		$this->conversation->messages_count = $this->conversation->messages()->count();
		$this->conversation->participations_count = $this->conversation->participations()->count();
		$this->conversation->save();
	}
}
