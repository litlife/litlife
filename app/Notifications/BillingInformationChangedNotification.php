<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BillingInformationChangedNotification extends Notification
{
	use Queueable;

	public $user;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($user)
	{
		$this->user = $user;
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
		$mail = (new MailMessage)
			->subject(__('notification.billing_information_changed.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.billing_information_changed.line'));

		foreach ($this->user->wallets()->latest()->get() as $wallet) {
			$mail->line(__('user_payment_detail.' . $wallet->type) . ': **' . $wallet->number . '**');
		}

		return $mail->action(__('notification.billing_information_changed.action'), route('users.wallet.payment_details', ['user' => $this->user]));
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
