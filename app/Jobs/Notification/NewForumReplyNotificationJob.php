<?php

namespace App\Jobs\Notification;

use App\Mail\NewForumReply;
use App\Notifications\NewForumReplyNotification;
use Illuminate\Foundation\Bus\Dispatchable;

class NewForumReplyNotificationJob
{
	use Dispatchable;

	protected $post;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct($post)
	{
		$this->post = $post;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		if (empty($this->post->parent))
			return null;

		$notifiable = $this->post->parent->create_user;

		if (!empty($notifiable))
			$notifiable->notify(new NewForumReplyNotification($this->post));
	}
}
