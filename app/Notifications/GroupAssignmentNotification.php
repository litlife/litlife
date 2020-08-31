<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GroupAssignmentNotification extends Notification
{
	public $groups;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($groups)
	{
		$this->groups = $groups->where('notify_assignment', true);
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
		if ($this->groups->count())
			return ['mail', 'database'];
		else
			return [];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return MailMessage
	 */
	public function toMail($notifiable)
	{
		$groups_name = implode(', ', $this->groups->pluck('name')->toArray());

		return (new MailMessage)
			->subject(__('notification.group_assigment.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.group_assigment.line', [
				'group_name' => $groups_name,
			]))
			->action(__('notification.group_assigment.action'), route('profile', ['user' => $notifiable]));
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		$groups_name = implode(', ', $this->groups->pluck('name')->toArray());

		return [
			'title' => __('notification.group_assigment.subject'),
			'description' => __('notification.group_assigment.line', [
				'group_name' => $groups_name,
			]),
			'url' => route('profile', ['user' => $notifiable])
		];
	}
}
