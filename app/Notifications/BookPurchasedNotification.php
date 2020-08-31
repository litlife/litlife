<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookPurchasedNotification extends Notification
{
	use Queueable;

	public $user_purchase;
	public $book;
	public $writers_names;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($user_purchase)
	{
		$this->user_purchase = $user_purchase;
		$this->book = $this->user_purchase->purchasable;
		$this->writers_names = implode(', ', $this->book->writers->pluck('name')->toArray());
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 * @return array
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
			->subject(__('notification.book_purchased.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.book_purchased.line', [
				'book_title' => $this->book->title,
				'writers_names' => $this->writers_names
			]))
			->action(__('notification.book_purchased.action'), route('books.show', ['book' => $this->book]));
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
			'title' => __('notification.book_purchased.subject'),
			'description' => __('notification.book_purchased.line', [
				'book_title' => $this->book->title,
				'writers_names' => $this->writers_names
			]),
			'url' => route('books.show', ['book' => $this->book])
		];
	}
}
