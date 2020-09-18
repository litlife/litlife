<?php

namespace App\Observers;

use App\Conversation;
use App\Jobs\DownloadExternalImages;
use App\Jobs\Notification\NewPersonalMessageNotificationJob;
use App\Message;
use App\Participation;
use Illuminate\Support\Facades\Artisan;

class MessageObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Message $message
	 * @return void
	 */
	public function creating(Message $message)
	{
		if ($message->recepient_id) {
			$conversation = Conversation::whereUsers($message->create_user_id, $message->recepient_id)
				->first();

			if (empty($conversation)) {
				$conversation = new Conversation;
				$conversation->latest_message_id = 0;
				$conversation->messages_count = 1;
				$conversation->participations_count = 0;
				$conversation->save();

				$participation = new Participation;
				$participation->user_id = $message->create_user_id;
				$conversation->participations()->save($participation);

				$participation = new Participation;
				$participation->user_id = $message->recepient_id;
				$conversation->participations()->save($participation);
			}

			$message->conversation()->associate($conversation);
			$message->setRelation('conversation', $conversation);
		}
	}

	public function updating(Message $message)
	{

	}

	public function created(Message $message)
	{
		$participations = $message->conversation->participations;
		$participations->loadMissing('user');

		foreach ($participations as $participation) {

			if ($message->isUserCreator($participation->user)) {
				$participation->new_messages_count = 0;
				$participation->latest_seen_message_id = $message->id;
			} else {
				$participation->new_messages_count++;
			}

			$participation->latest_message_id = $message->id;
			$participation->save();

			$participation->user->flushCacheNewMessages();
		}

		NewPersonalMessageNotificationJob::dispatch($message);
	}

	public function updated(Message $message)
	{

	}

	public function deleted(Message $message)
	{

	}

	public function restored(Message $message)
	{

	}

	public function saved(Message $message)
	{
		if ($message->isChanged('bb_text') and empty($message->external_images_downloaded)) {
			Artisan::call('images:download_external', [
				'model' => 'message', 'id' => $message->id
			]);
		}
	}
}