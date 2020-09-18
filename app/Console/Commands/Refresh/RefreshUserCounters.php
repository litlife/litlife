<?php

namespace App\Console\Commands\Refresh;

use App\Enums\ReadStatus;
use App\Events\BookFilesCountChanged;
use App\Jobs\User\UpdateUserAchievemetsCount;
use App\Jobs\User\UpdateUserAchieventsCount;
use App\Jobs\User\UpdateUserBlacklistsCount;
use App\Jobs\User\UpdateUserBookVotesCount;
use App\Jobs\User\UpdateUserCommentsCount;
use App\Jobs\User\UpdateUserConfirmedMailboxCount;
use App\Jobs\User\UpdateUserCreatedAuthorsCount;
use App\Jobs\User\UpdateUserCreatedBooksCount;
use App\Jobs\User\UpdateUserCreatedSequencesCount;
use App\Jobs\User\UpdateUserFavoriteAuthorsCount;
use App\Jobs\User\UpdateUserFavoriteBooksCount;
use App\Jobs\User\UpdateUserFavoriteSequencesCount;
use App\Jobs\User\UpdateUserFriendsCount;
use App\Jobs\User\UpdateUserPostsCount;
use App\Jobs\User\UpdateUserReadStatusCount;
use App\Jobs\User\UpdateUserSubscribersCount;
use App\Jobs\User\UpdateUserSubscriptionsCount;
use App\Jobs\User\UpdateUserTopicsCount;
use App\User;
use Illuminate\Console\Command;

class RefreshUserCounters extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:user_counters {id}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет все счетчики пользователя';

	protected $user;

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->user = User::any()->findOrFail($this->argument('id'));

		UpdateUserFriendsCount::dispatch($this->user);
		UpdateUserSubscriptionsCount::dispatch($this->user);
		UpdateUserSubscribersCount::dispatch($this->user);
		UpdateUserBlacklistsCount::dispatch($this->user);
		UpdateUserReadStatusCount::dispatch($this->user, ReadStatus::Readed);
		UpdateUserReadStatusCount::dispatch($this->user, ReadStatus::ReadLater);
		UpdateUserReadStatusCount::dispatch($this->user, ReadStatus::ReadNow);
		UpdateUserReadStatusCount::dispatch($this->user, ReadStatus::ReadNotComplete);
		UpdateUserReadStatusCount::dispatch($this->user, ReadStatus::NotRead);
		UpdateUserFavoriteAuthorsCount::dispatch($this->user);
		UpdateUserFavoriteBooksCount::dispatch($this->user);
		UpdateUserFavoriteSequencesCount::dispatch($this->user);
		UpdateUserBookVotesCount::dispatch($this->user);
		UpdateUserCommentsCount::dispatch($this->user);
		UpdateUserPostsCount::dispatch($this->user);
		UpdateUserAchievemetsCount::dispatch($this->user);
		UpdateUserCreatedSequencesCount::dispatch($this->user);
		UpdateUserCreatedBooksCount::dispatch($this->user);
		UpdateUserCreatedAuthorsCount::dispatch($this->user);
		$this->user->refreshCreatedTopicsCount();
		$this->user->refreshConfirmedMailboxCount();

		$this->user->flushNotViewedFriendsNewsCount();
		$this->user->purchasedBookCountRefresh();

		$this->user->flushCachedFavoriteBooksWithUpdatesCount();
		$this->user->refer_users_refresh();
		$this->user->balance(true);

		$this->updateParticipationCounters();
		$this->user->flushCacheNewMessages();

		$this->user->refresh_counters = null;
		$this->user->save();
	}

	protected function updateParticipationCounters()
	{
		$participations = $this->user->participations()->get();

		foreach ($participations as $participation) {
			$participation->updateNewMessagesCount();
			$participation->updateLatestMessage();
			$participation->save();
		}
	}
}
