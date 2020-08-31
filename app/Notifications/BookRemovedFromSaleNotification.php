<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookRemovedFromSaleNotification extends Notification
{
	use Queueable;

	public $book;
	public $writers_names;
	public $days;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($book)
	{
		$this->book = $book;
		$this->writers_names = implode(', ', $this->book->writers->pluck('name')->toArray());
		$this->days = config('litlife.book_removed_from_sale_cooldown_in_days');
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
		return ['mail', 'database'];
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
			->subject(__('notification.book_removed_from_sale.subject', ['book_title' => $this->book->title]))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.book_removed_from_sale.line', [
				'book_title' => $this->book->title,
				'writers_names' => $this->writers_names,
				'days' => $this->days
			]))
			->action(__('notification.book_removed_from_sale.action'), route('books.show', ['book' => $this->book]));
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
			'title' => __('notification.book_removed_from_sale.subject', ['book_title' => $this->book->title]),
			'description' => __('notification.book_removed_from_sale.line', [
				'book_title' => $this->book->title,
				'writers_names' => $this->writers_names,
				'days' => $this->days
			]),
			'url' => route('books.show', ['book' => $this->book])
		];
	}
}
