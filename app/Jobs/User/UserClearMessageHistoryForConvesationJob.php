<?php

namespace App\Jobs\User;

use App\Conversation;
use App\Message;
use App\User;
use Illuminate\Foundation\Bus\Dispatchable;

class UserClearMessageHistoryForConvesationJob
{
	use Dispatchable;

	private $user;
	private $conversation;

	/**
	 * Create a new job instance.
	 *
	 * @param User $user
	 * @param Conversation $conversation
	 * @return void
	 */
	public function __construct(User $user, Conversation $conversation)
	{
		$this->user = $user;
		$this->conversation = $conversation;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$participation = $this->conversation
			->participations()
			->where('user_id', $this->user->id)
			->first();

		if (!$participation)
			throw new \LogicException('The user must participate in the conversation');

		$this->conversation
			->messages()
			->notDeletedForUser($this->user)
			->chunkById(100, function ($messages) {
				foreach ($messages as $message) {
					$this->item($message);
				}
			});
	}

	public function item(Message $message)
	{
		$message->deleteForUser($this->user);
	}
}
