<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class NewWallReplyNotification extends Notification
{
	use Queueable;

	public $blog;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($blog)
	{
		$this->blog = $blog;
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
			'subject' => __('notification.wall_reply.subject'),
			'line' => __('notification.wall_reply.line', ['userName' => $this->blog->create_user->userName]),
			'action' => route('users.blogs.go', ['user' => $this->blog->owner, 'blog' => $this->blog])
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

		if ($notifiable->email_notification_setting->wall_reply) {
			if (!empty($notifiable->notice_email->email))
				$array[] = 'mail';
		}

		if ($notifiable->email_notification_setting->db_wall_reply) {
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
			->subject(__('notification.wall_reply.subject'))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.wall_reply.line', ['userName' => $this->blog->create_user->userName]))
			->action(__('notification.wall_reply.action'), route('users.blogs.go', ['user' => $this->blog->owner, 'blog' => $this->blog]))
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
			'title' => __('notification.wall_reply.subject'),
			'description' => __('notification.wall_reply.line', ['userName' => $this->blog->create_user->userName]),
			'url' => route('users.blogs.go', ['user' => $this->blog->owner, 'blog' => $this->blog])
		];
	}
}
