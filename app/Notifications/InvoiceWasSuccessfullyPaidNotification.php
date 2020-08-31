<?php

namespace App\Notifications;

use App\UserPaymentTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceWasSuccessfullyPaidNotification extends Notification
{
	use Queueable;

	public $transaction;

	/**
	 * Create a new notification instance.
	 *
	 * @param UserPaymentTransaction $transaction
	 * @return void
	 */
	public function __construct(UserPaymentTransaction $transaction)
	{
		$this->transaction = $transaction;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 * @return array
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
			->subject(__('notification.invoice_was_successfully_paid.subject'))
			->greeting(__('notification.greeting') . ', ' . $this->transaction->user->userName . '!')
			->line(__('notification.invoice_was_successfully_paid.line', [
				'sum' => $this->transaction->sum
			]))
			->action(__('notification.invoice_was_successfully_paid.action'), route('users.wallet', ['user' => $this->transaction->user]));
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
