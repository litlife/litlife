<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuthorSaleRequestRejectedNotification extends Notification
{
	use Queueable;

	public $author_sale_request;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($author_sale_request)
	{
		$this->author_sale_request = $author_sale_request;
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
			->subject(__('notification.author_sale_request_rejected.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.author_sale_request_rejected.line', ['author_name' => $this->author_sale_request->author->name]))
			->action(__('notification.author_sale_request_rejected.action'), route('authors.sales_requests.show', ['request' => $this->author_sale_request]));
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
			'title' => __('notification.author_sale_request_rejected.subject'),
			'description' => __('notification.author_sale_request_rejected.line', ['author_name' => $this->author_sale_request->author->name]),
			'url' => route('authors.sales_requests.show', ['request' => $this->author_sale_request])
		];
	}
}
