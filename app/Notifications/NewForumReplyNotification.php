<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class NewForumReplyNotification extends Notification
{
	use Queueable;

	public $post;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($post)
	{
		$this->post = $post;
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
			'subject' => __('notification.forum_reply.subject'),
			'line' => __('notification.forum_reply.line', ['userName' => $this->post->create_user->userName]),
			'action' => route('posts.go_to', ['post' => $this->post])
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
		if ($notifiable->email_notification_setting->forum_reply) {
			if (!empty($notifiable->notice_email->email))
				$array[] = 'mail';
		}

		if ($notifiable->email_notification_setting->db_forum_reply) {
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
			->subject(__('notification.forum_reply.subject'))
			->greeting(__('notification.greeting') . ', ' . $this->post->parent->create_user->userName . '!')
			->line(__('notification.forum_reply.line', ['userName' => $this->post->create_user->userName]))
			->action(__('notification.forum_reply.action'), route('posts.go_to', ['post' => $this->post]))
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
			'title' => __('notification.forum_reply.subject'),
			'description' => __('notification.forum_reply.line', ['userName' => $this->post->create_user->userName]),
			'url' => route('posts.go_to', ['post' => $this->post])
		];
	}
}
