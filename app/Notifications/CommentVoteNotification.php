<?php

namespace App\Notifications;

use App\CommentVote;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class CommentVoteNotification extends Notification
{
	use Queueable;

	public $comment_vote;

	/**
	 * Create a new notification instance.
	 *
	 * @param CommentVote $comment_vote
	 * @return void
	 */
	public function __construct(CommentVote $comment_vote)
	{
		$this->comment_vote = $comment_vote;
	}

	/**
	 * Get the broadcastable representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return BroadcastMessage
	 */
	public function toBroadcast($notifiable)
	{

	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 * @return  array
	 */
	public function via($notifiable)
	{
		$array = [];

		if ($notifiable->email_notification_setting->db_comment_vote_up) {
			$array[] = 'database';
		}

		return $array;
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return MailMessage
	 */
	public function toMail($notifiable)
	{

	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		return [
			'title' => __('notification.new_like_notification.comment.subject'),
			'description' => __('notification.new_like_notification.comment.line', [
				'userName' => $this->comment_vote->create_user->userName,
				'book_title' => Str::limit($this->comment_vote->comment->commentable->title, 30),
			]),
			'url' => route('comments.go', ['comment' => $this->comment_vote->comment])
		];
	}
}
