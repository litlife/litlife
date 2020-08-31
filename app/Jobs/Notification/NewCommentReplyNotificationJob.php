<?php

namespace App\Jobs\Notification;

use App\Comment;
use App\Mail\NewCommentReply;
use App\Notifications\NewCommentReplyNotification;
use Illuminate\Foundation\Bus\Dispatchable;

class NewCommentReplyNotificationJob
{
	use Dispatchable;

	public $create_user;
	protected $comment;

	/**
	 * Create a new job instance.
	 *
	 * @param Comment $comment
	 * @return void
	 */
	public function __construct(Comment $comment)
	{
		$this->comment = $comment;
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		// отсутсвует родительский комментарий
		if (empty($this->comment->parent))
			return null;

		$this->create_user = $this->comment->parent->create_user;

		$this->create_user->notify(new NewCommentReplyNotification($this->comment));
	}
}
