<?php

namespace App\Notifications;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserHasRegisteredNotification extends Notification
{
	use Queueable;

	public $user;
	public $password;

	/**
	 * Create a new notification instance.
	 *
	 * @param User $user
	 * @param string $password
	 * @return void
	 */
	public function __construct(User $user, $password = '')
	{
		$this->user = $user;
		$this->password = $password;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		return ['mail', 'database'];
	}

	/**
	 * Get the mail representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return MailMessage
	 */
	public function toMail($notifiable)
	{
		$email = $this->user->emails()
			->confirmed()
			->first();

		$markdown = \App\TextBlock::latestVersion('Приветствие')
			->getMarkdown();

		$lines = explode("\n", $markdown);

		$mail = (new MailMessage)
			->subject(__('notification.user_has_registered.subject'))
			->line(__('notification.user_has_registered.line'))
			->line(__('notification.user_has_registered.line2'))
			->line(__('notification.user_has_registered.line3', ['email' => $email->email]))
			->line(__('notification.user_has_registered.line4', ['password' => $this->password]));

		foreach ($lines as $line)
			$mail->line($line);

		$mail->greeting(__('notification.greeting') . ', ' . $this->user->userName . '!')
			->action(__('notification.user_has_registered.action'), route('welcome'));

		return $mail;
	}

	public function toArray($notifiable)
	{
		return [
			'title' => __('notification.user_has_registered.subject'),
			'description' => __('Click to go to the welcome page'),
			'url' => route('welcome')
		];
	}
}
