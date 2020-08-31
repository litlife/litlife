<?php

namespace App\Notifications;

use App\UserOutgoingPayment;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WithdrawalSuccessNotification extends Notification
{
	use Queueable;

	public $payment;

	/**
	 * Create a new notification instance.
	 *
	 * @param UserOutgoingPayment $payment
	 * @return void
	 */
	public function __construct(UserOutgoingPayment $payment)
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
			->subject(__('notification.withdrawal_success.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.withdrawal_success.line', [
				'sum' => abs($this->payment->transaction->sum) - $this->payment->getPayoutComission(),
				'comission' => $this->payment->getPayoutComission()
			]))
			->line(__('notification.withdrawal_success.line2', [
				'payment_type' => __('user_payment_transaction.payment_types_array.' . $this->payment->payment_type),
				'purse' => $this->payment->purse
			]))
			->line(__('notification.withdrawal_success.line3', [
				'transaction_id' => $this->payment->transaction->id
			]))
			->action(__('notification.withdrawal_success.action'), route('users.wallet', ['user' => $this->payment->transaction->user]));
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
