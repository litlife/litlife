<?php

namespace App\Notifications;

use App\SupportQuestionMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewSupportResponseNotification extends Notification
{
	use Queueable;

	public $message;
	public $supportQuestion;

	/**
	 * Create a new notification instance.
	 *
	 * @param SupportQuestionMessage $message
	 * @return void
	 */
	public function __construct(SupportQuestionMessage $message)
	{
		$this->message = $message;
		$this->supportQuestion = $this->message->supportQuestion;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 * @return  array
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
		return (new MailMessage)
			->subject(__('New response from the support service'))
			->greeting(__('Hello') . ', ' . $this->supportQuestion->create_user->userName . '!')
			->line(__('We have answered your question with the subject «:title»', ['title' => $this->supportQuestion->title]))
			->action(__('Go to answer'), route('support_questions.show', ['support_question' => $this->supportQuestion->id]));
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
			'title' => __('New response from the support service'),
			'description' => __('We have answered your question with the subject «:title»', ['title' => $this->supportQuestion->title]),
			'url' => route('support_questions.show', ['support_question' => $this->supportQuestion->id])
		];
	}
}
