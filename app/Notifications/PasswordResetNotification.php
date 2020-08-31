<?php

namespace App\Notifications;

use App\PasswordReset;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetNotification extends Notification
{
	use Queueable;

	public $passwordReset;

	/**
	 * Create a new notification instance.
	 *
	 * @param PasswordReset $passwordReset
	 * @return void
	 */
	public function __construct(PasswordReset $passwordReset)
	{
		$this->passwordReset = $passwordReset;
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
			->subject(__('notification.password_reset.subject'))
			->greeting(__('notification.greeting') . ', ' . $this->passwordReset->user->userName . '!')
			->line(__('notification.password_reset.line'))
			->action(__('notification.password_reset.action'), route('password.reset_form', $this->passwordReset->token));
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
