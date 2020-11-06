<?php

namespace App\Notifications;

use App\Author;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuthorPageNeedsToBeVerifiedNotification extends Notification
{
	use Queueable;

	public $author;

	/**
	 * Create a new notification instance.
	 *
	 * @param Author $author
	 * @return void
	 */
	public function __construct(Author $author)
	{
		$this->author = $author;
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
		/*
		return (new MailMessage)
			->subject(__('notification.author_sale_request_accepted.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.author_sale_request_accepted.line', ['author_name' => $this->author_sale_request->author->name]))
			->action(__('notification.author_sale_request_accepted.action'), route('authors.show', ['author' => $this->author_sale_request->author]));
		*/
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
			'title' => __('It looks like you created your author page.'),
			'description' => __('If you have created your author page, please do not forget to verify it to get access to editing books and other features'),
			'url' => route('authors.verification.request', ['author' => $this->author])
		];
	}
}
