<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSubscriberNotification extends Notification
{
	use Queueable;

	public $subscriber;

	/**
	 * Create a new notification instance.
	 *
	 * @param User $subscriber
	 * @return void
	 */
	public function __construct(User $subscriber)
	{
		$this->subscriber = $subscriber;
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
		return [
			'title' => __('notification.new_subscriber.subject'),
			'description' => __('notification.new_subscriber.line', [
				'userName' => $this->subscriber->userName,
			]),
			'url' => route('profile', $this->subscriber)
		];
	}
}
