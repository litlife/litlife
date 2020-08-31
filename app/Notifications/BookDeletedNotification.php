<?php

namespace App\Notifications;

use App\Book;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookDeletedNotification extends Notification
{
	use Queueable;

	public $book;
	public $reason;

	/**
	 * Create a new notification instance.
	 *
	 * @param Book $book
	 * @param string $reason
	 * @return void
	 */
	public function __construct(Book $book, $reason)
	{
		$this->book = $book;
		$this->reason = $reason;
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
		return ['database'];
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
		$description = __('notification.book_deleted.line', [
			'book_title' => $this->book->title,
			'writers_names' => optional($this->book->writers()->first())->name,
		]);

		if (!empty($this->reason)) {
			$description .= ' ' . __('notification.book_deleted.reason', ['reason' => $this->reason]);
		}

		return [
			'title' => __('notification.book_deleted.subject'),
			'description' => $description,
			'url' => route('books.show', ['book' => $this->book])
		];
	}
}
