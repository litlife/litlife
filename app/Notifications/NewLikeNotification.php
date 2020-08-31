<?php

namespace App\Notifications;

use App\Blog;
use App\Book;
use App\Collection;
use App\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewLikeNotification extends Notification
{
	use Queueable;

	public $like;

	/**
	 * Create a new notification instance.
	 *
	 * @return void
	 */
	public function __construct($like)
	{
		$this->like = $like;
	}

	/**
	 * Get the notification's delivery channels.
	 *
	 * @param mixed $notifiable
	 * @return array
	 */
	public function via($notifiable)
	{
		$array = [];

		if ($notifiable->email_notification_setting->db_like) {
			$array[] = 'database';
		}

		return $array;
	}

	/**
	 * Get the array representation of the notification.
	 *
	 * @param mixed $notifiable
	 * @return array
	 */
	public function toArray($notifiable)
	{
		$array = [];

		if ($this->like->likeable instanceof Blog) {
			$array['title'] = __('notification.new_like_notification.blog.subject');
			$array['description'] = __('notification.new_like_notification.blog.line', ['userName' => $this->like->create_user->userName]);
			$array['url'] = route('users.blogs.go', ['user' => $this->like->likeable->owner, 'blog' => $this->like->likeable]);
		} elseif ($this->like->likeable instanceof Post) {
			$array['title'] = __('notification.new_like_notification.post.subject');
			$array['description'] = __('notification.new_like_notification.post.line', ['userName' => $this->like->create_user->userName]);
			$array['url'] = route('posts.go_to', ['post' => $this->like->likeable]);
		} elseif ($this->like->likeable instanceof Book) {
			$array['title'] = __('notification.new_like_notification.book.subject');
			$array['description'] = __('notification.new_like_notification.book.line', [
				'book_title' => $this->like->likeable->title,
				'userName' => $this->like->create_user->userName
			]);
			$array['url'] = route('books.show', ['book' => $this->like->likeable]);
		} elseif ($this->like->likeable instanceof Collection) {
			$array['title'] = __('notification.new_like_notification.collection.subject');
			$array['description'] = __('notification.new_like_notification.collection.line', [
				'collection_title' => $this->like->likeable->title,
				'userName' => $this->like->create_user->userName
			]);
			$array['url'] = route('collections.show', ['collection' => $this->like->likeable]);
		}

		if (!empty($array['title']) or !empty($array['description']))
			return $array;
	}
}
