<?php

namespace App\Jobs\Notification;

use App\Mail\NewWallMessage;
use App\Notifications\NewWallMessageNotification;
use Illuminate\Foundation\Bus\Dispatchable;

class NewWallMessageNotificationJob
{
	use Dispatchable;

	protected $blog;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($blog)
	{
		$this->blog = $blog;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$notifiable = $this->blog->owner;

		// если пользователь владелец стены
		if ($notifiable->id == $this->blog->create_user->id)
			return null;

		$notifiable->notify(new NewWallMessageNotification($this->blog));
	}
}
