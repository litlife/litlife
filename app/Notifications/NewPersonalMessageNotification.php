<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class NewPersonalMessageNotification extends Notification
{
	use Queueable;

	public $message;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($message)
	{
		$this->message = $message;
	}

	/**
	 * Get the broadcastable representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return BroadcastMessage
	 */
	public function toBroadcast($notifiable)
	{
		return new BroadcastMessage([
			'subject' => __('notification.new_personal_message.subject'),
			'line' => __('notification.new_personal_message.line', ['userName' => $this->message->create_user->userName]),
			'action' => route('users.messages.index', ['user' => $this->message->create_user])
		]);
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 * @return  array
	 */
	public function via($notifiable)
	{
		$array = [];

		// проверяем хочет ли владелец комментария на который отправляется ответ получать уведомления об ответах
		if ($notifiable->email_notification_setting->private_message) {
			if (!empty($notifiable->notice_email->email)) {
				$array[] = 'mail';
			}
		}

		return $array;
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
			->subject(__('notification.new_personal_message.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.new_personal_message.line', ['userName' => $this->message->create_user->userName]))
			->action(__('notification.new_personal_message.action'), route('users.messages.index', ['user' => $this->message->create_user]))
			->line('<a href="' . URL::temporarySignedRoute('users.settings.email_delivery.edit.without_authorization', now()->addMonth(), ['user' => $notifiable->id]) . '">' . __('common.unsubscribe') . '</a>');
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
