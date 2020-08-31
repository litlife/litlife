<?php

namespace App\Observers;

use App\Collection;
use App\Comment;
use App\Jobs\Author\UpdateAuthorCommentsCount;
use App\Jobs\DownloadExternalImages;
use App\Jobs\Notification\NewCommentReplyNotificationJob;
use App\Jobs\User\UpdateUserCommentsCount;
use App\Notifications\NewCommentInCollectionNotification;
use App\UserAgent;
use Cache;
use Illuminate\Support\Facades\Artisan;

class CommentObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Comment $comment
	 * @return void
	 */
	public function creating(Comment $comment)
	{
		$comment->autoAssociateAuthUser();

		$comment->ip = request()->ip();

		if (!$comment->isPrivate()) {
			// проверяем состоит ли пользователь на проверке комментариев и сообщений на форуме
			if ($comment->isMustBeSentForReview())
				// если да, то отправяем комментарий на модерацию
				$comment->statusSentForReview();
			else
				$comment->statusAccepted();

			if ($comment->isMustBeHideFromTop())
				$comment->hide_from_top = true;
			else
				$comment->hide_from_top = false;
		}

		$this->level($comment);

		$comment->user_agent_id = UserAgent::getCurrentId();

		if (!empty($comment->parent)) {
			$comment->commentable_type = $comment->parent->commentable_type;
			$comment->commentable_id = $comment->parent->commentable_id;

			if (!empty($comment->parent->origin_commentable_id))
				$comment->origin_commentable_id = $comment->parent->origin_commentable_id;
		}

		if (empty($comment->origin_commentable_id))
			$comment->origin_commentable_id = $comment->commentable_id;
	}

	private function level(Comment $comment)
	{
		preg_match_all('/[0-9]+/iu', $comment->tree, $matches);

		$comment->level = count($matches[0]);
	}

	public function updating(Comment $comment)
	{
		if ($comment->isChanged('bb_text')) {
			if (!$comment->isPrivate()) {
				if ($comment->isMustBeSentForReview())
					$comment->statusSentForReview();
				else
					$comment->statusAccepted();
			}
		}
	}

	public function updated(Comment $comment)
	{
		if ($comment->isChanged('status')) {
			Comment::flushCachedOnModerationCount();
		}
	}

	public function created(Comment $comment)
	{
		$this->created_or_deleted($comment);

		Cache::forever('comments_count_refresh', 'true');

		$this->notification($comment);
	}

	public function created_or_deleted(&$comment)
	{
		$parent = $comment->parent;

		if (isset($parent)) {
			$parent->updateChildrenCount();
		}

		if (!empty($comment->commentable)) {
			if ($comment->isBookType()) {
				$comment->commentable->refreshCommentsCount();
				$comment->commentable->save();

				if ($comment->commentable->authors->count()) {
					foreach ($comment->commentable->authors as $author) {
						UpdateAuthorCommentsCount::dispatch($author);
					}
				}
			} elseif ($comment->isCollectionType()) {
				$comment->commentable->refreshCommentCount();
				$comment->commentable->save();
			}
		}

		if (!empty($comment->create_user))
			UpdateUserCommentsCount::dispatch($comment->create_user);

		// если комментарий доавблен или удален с модерации, то нужно обновить счетчик

		if ($comment->isSentForReview())
			Comment::flushCachedOnModerationCount();
	}

	private function notification(Comment $comment)
	{
		NewCommentReplyNotificationJob::dispatch($comment);

		if ($comment->isCollectionType())
			$this->subscribedUsersNotification($comment->commentable, $comment);
	}

	public function subscribedUsersNotification(Collection $collection, Comment $comment)
	{
		$subscriptions = $collection
			->eventNotificationSubscriptions()
			->with('notifiable_user')
			->get();

		foreach ($subscriptions as $subscription) {

			$user = $subscription->notifiable_user;

			if (empty($user))
				break;

			if ($user->id == $comment->create_user->id)
				break;

			if (!empty($comment->parent)) {
				if ($user->id == $comment->parent->create_user->id) {
					break;
				}
			}

			$user->notify(new NewCommentInCollectionNotification($comment));
		}
	}

	public function deleted(Comment $comment)
	{
		$this->created_or_deleted($comment);

		Cache::forever('comments_count_refresh', 'true');
	}

	public function restored(Comment $comment)
	{
		$this->created_or_deleted($comment);

		Cache::forever('comments_count_refresh', 'true');
	}

	public function saved(Comment $comment)
	{
		if ($comment->isChanged('bb_text') and empty($comment->external_images_downloaded)) {
			Artisan::call('images:download_external', [
				'model' => 'comment', 'id' => $comment->id
			]);
		}
	}
}