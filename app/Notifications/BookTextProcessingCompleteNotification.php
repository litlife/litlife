<?php

namespace App\Notifications;

use App\BookTextProcessing;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookTextProcessingCompleteNotification extends Notification
{
	use Queueable;

	public $processing;

	/**
	 * Create a new notification instance.
	 *
	 * @param BookTextProcessing $processing
	 * @return void
	 */
	public function __construct(BookTextProcessing $processing)
	{
		$this->processing = $processing;
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
			'title' => __('notification.book_text_processing_complete.line', [
				'book_title' => $this->processing->book->title
			]),
			'description' => __('notification.book_text_processing_complete.subject'),
			'url' => route('books.show', ['book' => $this->processing->book])
		];
	}
}
