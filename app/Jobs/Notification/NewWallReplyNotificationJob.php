<?php

namespace App\Jobs\Notification;

use App\Mail\NewWallReply;
use App\Notifications\NewWallReplyNotification;
use Illuminate\Foundation\Bus\Dispatchable;

class NewWallReplyNotificationJob
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
		if (empty($this->blog->parent))
			return null;

		$notifiable = $this->blog->parent->create_user;

		$notifiable->notify(new NewWallReplyNotification($this->blog));

	}
}
