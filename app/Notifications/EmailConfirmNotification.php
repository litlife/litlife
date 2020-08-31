<?php

namespace App\Notifications;

use App\UserEmailToken;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmailConfirmNotification extends Notification
{
	use Queueable;

	public $token;

	/**
	 * Create a new notification instance.
	 *
	 * @param UserEmailToken $token
	 * @return void
	 */
	public function __construct(UserEmailToken $token)
	{
		$this->token = $token;
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
			->subject(__('notification.email_confirm.subject'))
			->greeting(__('notification.greeting') . ', ' . $this->token->email->user->userName . '!')
			->line(__('notification.email_confirm.line', ['email' => $this->token->email->email]))
			->action(__('notification.email_confirm.action'), route('email.confirm', ['email' => $this->token->email, 'token' => $this->token->token]));
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
