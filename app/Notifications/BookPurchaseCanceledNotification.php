<?php

namespace App\Notifications;

use App\UserPurchase;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookPurchaseCanceledNotification extends Notification
{
	use Queueable;

	public $purchase;

	/**
	 * Create a new notification instance.
	 *
	 * @param UserPurchase $purchase
	 * @return void
	 */
	public function __construct(UserPurchase $purchase)
	{
		$this->purchase = $purchase;
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
		return (new MailMessage)
			->subject(__('notification.purchase_canceled.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.purchase_canceled.line', [
				'book_title' => $this->purchase->purchasable->title,
				'writers_names' => optional($this->purchase->purchasable->writers()->first())->name
			]))
			->action(__('notification.purchase_canceled.action'), route('users.wallet', ['user' => $notifiable]));
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
			'title' => __('notification.purchase_canceled.subject'),
			'description' => __('notification.purchase_canceled.line', [
				'book_title' => $this->purchase->purchasable->title,
				'writers_names' => optional($this->purchase->purchasable->writers()->first())->name
			]),
			'url' => route('users.wallet', ['user' => $notifiable])
		];
	}
}
