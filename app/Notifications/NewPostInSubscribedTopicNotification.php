<?php

namespace App\Notifications;

use App\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPostInSubscribedTopicNotification extends Notification
{
	use Queueable;

	public $post;

	/**
	 * Create a new notification instance.
	 *
	 * @param Post $post
	 * @return void
	 */
	public function __construct(Post $post)
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
			->subject(__('notification.new_post_in_subscribed_topic.subject', ['topic_title' => $this->post->topic->name]))
			->greeting(__('notification.greeting') . ', ' . $notifiable->userName . '!')
			->line(__('notification.new_post_in_subscribed_topic.line', ['user_name' => $this->post->create_user->userName, 'topic_title' => $this->post->topic->name]))
			->action(__('notification.new_post_in_subscribed_topic.action'), route('posts.go_to', ['post' => $this->post]))
			->line('<a href="' . route('topics.unsubscribe', $this->post->topic) . '">' . __('topic.unsubscribe_from_receiving_notifications_about_new_posts_in_this_topic') . '</a>');
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
			'title' => __('notification.new_post_in_subscribed_topic.subject', ['topic_title' => $this->post->topic->name]),
			'description' => __('notification.new_post_in_subscribed_topic.line', ['user_name' => $this->post->create_user->userName, 'topic_title' => $this->post->topic->name]),
			'url' => route('posts.go_to', ['post' => $this->post])
		];
	}
}
