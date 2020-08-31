<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationToSellBooksNotification extends Notification
{
	use Queueable;


	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct()
	{

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
		return ['mail'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @return MailMessage
	 */
	public function toMail()
	{
		return (new MailMessage)
			->subject(__('notification.invitation_to_sell_books.subject'))
			->greeting(__('notification.invitation_to_sell_books.greeting') . '!')
			->action(__('notification.invitation_to_sell_books.action'), route('authors.how_to_start_selling_books'))
			->markdown('vendor.notifications.invitation_to_sell_books');
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
			//
		];
	}
}
