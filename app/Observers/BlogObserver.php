<?php

namespace App\Observers;

use App\Blog;
use App\Jobs\DownloadExternalImages;
use App\Jobs\Notification\NewWallMessageNotificationJob;
use App\Jobs\Notification\NewWallReplyNotificationJob;
use App\User;
use App\UserAgent;
use Illuminate\Support\Facades\Artisan;

class BlogObserver
{
	/**
	 * Listen to the User created event.
	 *
	 * @param Blog $blog
	 * @return void
	 */
	public function creating(Blog $blog)
	{
		$blog->autoAssociateAuthUser();

		$blog->updateLevel();

		$blog->user_agent_id = UserAgent::getCurrentId();

		// проверяем состоит ли пользователь на проверке комментариев и сообщений на форуме
		if (!$blog->isCreateOwner() and $blog->isMustBeSentForReview()) {
			// если да, то отправяем комментарий на модерацию
			$blog->statusSentForReview();
		} else {
			$blog->statusAccepted();
		}
	}

	public function updating(Blog $blog)
	{

	}

	public function created(Blog $blog)
	{
		$this->created_or_deleted($blog);

		$this->flushNotViewedFriendsNewsCount($blog);

		$this->notification($blog);
	}

	public function created_or_deleted(&$blog)
	{
		$parent = $blog->parent;

		if (isset($parent)) {
			$parent->updateChildrenCount();
		}

		$blog->owner->data->blog_record_comment_count = $blog->owner->blog()->roots()->count();
		$blog->owner->data->save();

		$this->flushCounterIfOnCheck($blog);
	}

	private function flushCounterIfOnCheck($item)
	{
		if ($item->isSentForReview())
			Blog::flushCachedOnModerationCount();
	}

	public function flushNotViewedFriendsNewsCount(Blog $blog)
	{
		if (!empty($blog->owner)) {
			$users = $blog->owner
				->friendsAndSubscriptions()
				->select(['user_relations.user_id'])
				->get()
				->pluck('user_id')
				->toArray();

			foreach ($users as $id)
				User::flushNotViewedFriendsNewsCountById($id);
		}
	}

	private function notification(Blog $blog)
	{
		if (empty($blog->parent))
			NewWallMessageNotificationJob::dispatch($blog);
		else
			NewWallReplyNotificationJob::dispatch($blog);
	}

	public function deleted(Blog $blog)
	{
		$this->created_or_deleted($blog);

		$this->flushNotViewedFriendsNewsCount($blog);

		$this->delete_childs($blog);
	}

	public function delete_childs(Blog $blog)
	{
		$descendants = Blog::childs($blog->id)->get();

		foreach ($descendants as $descendant) {
			$descendant->delete();
		}
	}

	public function restoring(Blog $blog)
	{
		$this->restore_childs($blog);
	}

	public function restore_childs(Blog $blog)
	{
		//dd($blog);
		if (!empty($blog->deleted_at)) {
			$descendants = Blog::childs($blog->id)
				->onlyTrashed()
				->whereTime('deleted_at', '>=', $blog->deleted_at)
				->get();

			foreach ($descendants as $descendant) {
				$descendant->restore();
			}
		}
	}

	public function restored(Blog $blog)
	{
		$this->created_or_deleted($blog);

		$this->flushNotViewedFriendsNewsCount($blog);
	}

	public function saved(Blog $blog)
	{
		if ($blog->isChanged('bb_text') and empty($blog->external_images_downloaded)) {
			Artisan::call('images:download_external', [
				'model' => 'blog', 'id' => $blog->id
			]);
		}
	}
}