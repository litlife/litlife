<?php

namespace App\Jobs\Notification;

use App\Mail\NewPersonalMessage;
use App\Message;
use App\Notifications\NewPersonalMessageNotification;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewPersonalMessageNotificationJob
{
	use Dispatchable, SerializesModels;

	protected $message;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct(Message $message)
	{
		$this->message = $message;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		foreach ($this->message->conversation->participations as $participation) {

			if ($participation->user_id != $this->message->create_user_id) {
				if (!$participation->user->email_notification_setting->private_message)
					return null;

				if (empty($participation->user->notice_email->email))
					return null;

				$participation->user->notify(new NewPersonalMessageNotification($this->message));
			}
		}


	}
}
