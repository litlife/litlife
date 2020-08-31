<?php

namespace App\Notifications;

use App\Manager;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AuthorManagerAcceptedNotification extends Notification
{
	use Queueable;

	public $manager;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct(Manager $manager)
	{
		$this->manager = $manager;
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
			->subject(__('notification.author_manager_request_accepted.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.author_manager_request_accepted.line', ['author_name' => $this->manager->manageable->name]))
			->action(__('notification.author_manager_request_accepted.action'), route('authors.show', ['author' => $this->manager->manageable]));
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
			'title' => __('notification.author_manager_request_accepted.subject'),
			'description' => __('notification.author_manager_request_accepted.line', ['author_name' => $this->manager->manageable->name]),
			'url' => route('authors.show', ['author' => $this->manager->manageable])
		];
	}
}
