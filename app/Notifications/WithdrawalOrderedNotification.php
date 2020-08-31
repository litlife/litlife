<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalOrderedNotification extends Notification
{
	use Queueable;

	public $payment;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($payment)
	{
		$this->payment = $payment;
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
			->subject(__('notification.withdrawal_ordered.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.withdrawal_ordered.line', [
				'sum' => abs($this->payment->transaction->sum),
			]))
			->line(__('notification.withdrawal_ordered.line2', [
				'payment_type' => __('user_payment_transaction.payment_types_array.' . $this->payment->payment_type),
				'purse' => $this->payment->purse,
			]))
			->line(__('notification.withdrawal_ordered.line3', [
				'transaction_id' => $this->payment->transaction->id
			]))
			->line(__('notification.withdrawal_ordered.line4'))
			->action(__('notification.withdrawal_ordered.action'), route('users.wallet', ['user' => $this->payment->transaction->user]));
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
