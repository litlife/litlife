<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class NewCommentReplyNotification extends Notification
{
	use Queueable;

	public $comment;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($comment)
	{
		$this->comment = $comment;
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
			'subject' => __('notification.comment_reply.subject'),
			'line' => __('notification.comment_reply.line', ['userName' => $this->comment->create_user->userName]),
			'action' => route('comments.go', ['comment' => $this->comment])
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
		if ($notifiable->email_notification_setting->comment_reply) {
			if (!empty($notifiable->notice_email->email)) {
				$array[] = 'mail';
			}
		}

		if ($notifiable->email_notification_setting->db_comment_reply) {
			$array[] = 'database';
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
			->subject(__('notification.comment_reply.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.comment_reply.line', ['userName' => $this->comment->create_user->userName]))
			->action(__('notification.comment_reply.action'), route('comments.go', ['comment' => $this->comment]))
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
			'title' => __('notification.comment_reply.subject'),
			'description' => __('notification.comment_reply.line', ['userName' => $this->comment->create_user->userName]),
			'url' => route('comments.go', ['comment' => $this->comment])
		];
	}
}
