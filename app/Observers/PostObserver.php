<?php

namespace App\Observers;

use App\Events\ForumCountOfPostsHasChanged;
use App\Events\TopicCountOfPostsHasChanged;
use App\Jobs\DownloadExternalImages;
use App\Jobs\Notification\NewForumReplyNotificationJob;
use App\Jobs\User\UpdateUserPostsCount;
use App\Notifications\NewPostInSubscribedTopicNotification;
use App\Post;
use App\Topic;
use App\UserAgent;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

class PostObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Post $post
	 * @return void
	 */
	public function creating(Post $post)
	{
		$post->autoAssociateAuthUser();
		$post->forum_id = $post->topic->forum->id;
		$post->private = $post->topic->forum->private;

		if ($post->isMustBeSentForReview()) {
			$post->statusSentForReview();
		} else {
			$post->statusAccepted();
		}

		$post->updateLevel();

		$post->user_agent_id = UserAgent::getCurrentId();
	}

	public function updating(Post $post)
	{

	}

	public function created(Post $post)
	{
		$this->parentPostRefreshChildPostCount($post);

		$this->flushCounterIfOnCheck($post);

		$this->refreshTopicCounters($post->topic);
		$this->refreshForumCounters(optional($post->topic)->forum);
		$this->updateCreateUserPostsCount($post);

		Cache::forever('posts_count_refresh', 'true');

		$this->notification($post);

		Topic::refreshLatestTopics();
	}

	/**
	 * Пересчитываем количество комментариев у родительского поста
	 *
	 * @param Post $post
	 * @return void
	 */

	private function parentPostRefreshChildPostCount($post)
	{
		$parent = $post->parent;

		if (!empty($parent)) {
			$parent->updateChildrenCount();
		}
	}

	private function flushCounterIfOnCheck($post)
	{
		// если комментарий доавблен или удален с модерации, то нужно обновить счетчик

		if ($post->isSentForReview())
			Post::flushCachedOnModerationCount();
	}

	public function refreshTopicCounters($topic)
	{
		if (!empty($topic)) {
			$topic->postsCountRefresh();
			$topic->lastPostRefresh();
			$topic->save();
		}
	}

	public function refreshForumCounters($forum)
	{
		if (!empty($forum)) {
			$forum->postsCountRefresh();
			$forum->lastPostRefresh();
			$forum->save();
		}
	}

	public function updateCreateUserPostsCount($post)
	{
		if (empty($post->create_user)) {
			$create_user = $post->create_user()->any()->first();

			if (!empty($create_user))
				UpdateUserPostsCount::dispatch($create_user);
		} else {
			UpdateUserPostsCount::dispatch($post->create_user);
		}
	}

	public function notification(Post $post)
	{
		NewForumReplyNotificationJob::dispatch($post);

		$query = $post->topic->subscribed_users()
			->where('users.id', '!=', $post->create_user->id);

		if ($parent = $post->parent) {
			if ($user = $parent->create_user)
				$query->where('users.id', '!=', $user->id);
		}

		$subscribed_users = $query->get();

		if ($subscribed_users->isNotEmpty()) {
			foreach ($subscribed_users as $user) {
				$user->notify(new NewPostInSubscribedTopicNotification($post));
			}
		}
	}

	public function deleted(Post $post)
	{
		$this->parentPostRefreshChildPostCount($post);

		$this->flushCounterIfOnCheck($post);

		$this->refreshTopicCounters($post->topic);
		$this->refreshForumCounters(optional($post->topic)->forum);
		$this->updateCreateUserPostsCount($post);

		Cache::forever('posts_count_refresh', 'true');

		Topic::refreshLatestTopics();
	}

	public function restored(Post $post)
	{
		$this->parentPostRefreshChildPostCount($post);

		$this->flushCounterIfOnCheck($post);

		$this->refreshTopicCounters($post->topic);
		$this->refreshForumCounters(optional($post->topic)->forum);
		$this->updateCreateUserPostsCount($post);

		Cache::forever('posts_count_refresh', 'true');

		Topic::refreshLatestTopics();
	}

	public function saved(Post $post)
	{
		if ($post->isChanged('bb_text') and empty($post->external_images_downloaded)) {
			Artisan::call('images:download_external', [
				'model' => 'post', 'id' => $post->id
			]);
		}
	}
}