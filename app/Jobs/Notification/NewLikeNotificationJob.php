<?php

namespace App\Jobs\Notification;

use App\Blog;
use App\Book;
use App\Collection;
use App\Like;
use App\Notifications\NewLikeNotification;
use App\Post;
use Illuminate\Foundation\Bus\Dispatchable;

class NewLikeNotificationJob
{
	use Dispatchable;

	protected $like;

	/**
	 * Create a new job instance.
	 *
	 * @param Like $like
	 * @return void
	 */
	public function __construct(Like $like)
	{
		$this->like = $like;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$likeable = $this->like->likeable;

		if (
			($this->like->likeable instanceof Blog)
			or ($this->like->likeable instanceof Book)
			or ($this->like->likeable instanceof Post)
			or ($this->like->likeable instanceof Collection)
		) {
			$notifiable = $likeable->create_user;

			if (!empty($notifiable))
				$notifiable->notify(new NewLikeNotification($this->like));
		}
	}
}
