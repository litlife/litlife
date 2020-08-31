<?php

namespace App\Notifications;

use App\BookParse;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Notification;

class BookFinishParseNotification extends Notification
{
	use Dispatchable;

	public $book_parse;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(BookParse $book_parse)
	{
		$this->book_parse = $book_parse;
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

		if ($notifiable->email_notification_setting->db_book_finish_parse) {
			$array[] = 'database';
		}

		return $array;
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
			'title' => __('notification.book_finish_parse.subject', ['title' => $this->book_parse->book->title]),
			'url' => route('books.show', ['book' => $this->book_parse->book])
		];
	}
}
