<?php

namespace App\Notifications;

use App\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentInCollectionNotification extends Notification
{
	use Queueable;

	public $comment;
	public $collection;

	/**
	 * Create a new notification instance.
	 *
	 * @param Comment $comment
	 * @return void
	 */
	public function __construct(Comment $comment)
	{
		$this->comment = $comment;
		$this->collection = $this->comment->commentable;
	}

	/**
	 * Get the broadcastable representation of the notification.
	 *
	 * @param mixed $notifiable
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

		$array[] = 'mail';
		$array[] = 'database';

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
		return (new MailMessage)
			->subject(__('notification.new_comment_in_collection.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.new_comment_in_collection.line', [
				'create_user_name' => $this->comment->create_user->userName,
				'collection_title' => $this->collection->title
			]))
			->action(__('notification.new_comment_in_collection.action'), route('comments.go', ['comment' => $this->comment]))
			->line('<a href="' . route('collections.event_notification_subcriptions.toggle', $this->collection) . '">' .
				__('collection.unsubscribe_from_notifications') . '</a>');
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
			'title' => __('notification.new_comment_in_collection.subject'),
			'description' => __('notification.new_comment_in_collection.line', [
				'create_user_name' => $this->comment->create_user->userName,
				'collection_title' => $this->collection->title
			]),
			'url' => route('comments.go', ['comment' => $this->comment])
		];
	}
}
