<?php

namespace App\Notifications;

use App\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationNotification extends Notification
{
	use Queueable;

	public $invitation;

	/**
	 * Create a new notification instance.
	 *
	 * @param Invitation $invitation
	 * @return void
	 */
	public function __construct(Invitation $invitation)
	{
		$this->invitation = $invitation;
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
	 * @param mixed $notifiable
	 * @return MailMessage
	 */
	public function toMail($notifiable)
	{
		return (new MailMessage)
			->subject(__('notification.invitation.subject'))
			->greeting(__('notification.greeting') . '!')
			->line(__('notification.invitation.line'))
			->action(__('notification.invitation.action'), route('users.registration', $this->invitation->token));
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
