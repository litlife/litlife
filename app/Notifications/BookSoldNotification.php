<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookSoldNotification extends Notification
{
	use Queueable;

	public $user_purchase;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($user_purchase)
	{
		$this->user_purchase = $user_purchase;
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
			->subject(__('notification.book_sold.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.book_sold.line', [
				'sum' => abs($this->user_purchase->seller_transaction->sum),
				'user_name' => $this->user_purchase->buyer->userName,
				'book_title' => $this->user_purchase->purchasable->title
			]))
			->action(__('notification.book_sold.action'), route('users.wallet', ['user' => $this->user_purchase->seller]));
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
			'title' => __('notification.book_sold.subject'),
			'description' => __('notification.book_sold.line', [
				'sum' => abs($this->user_purchase->seller_transaction->sum),
				'user_name' => $this->user_purchase->buyer->userName,
				'book_title' => $this->user_purchase->purchasable->title
			]),
			'url' => route('users.wallet', ['user' => $this->user_purchase->seller])
		];
	}
}
