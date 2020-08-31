<?php

namespace App\Console\Commands\Refresh;

use App\Events\BookFilesCountChanged;
use App\Jobs\User\UpdateUserBlacklistsCount;
use App\Jobs\User\UpdateUserFriendsCount;
use App\Jobs\User\UpdateUserSubscribersCount;
use App\Jobs\User\UpdateUserSubscriptionsCount;
use App\UserRelation;
use Illuminate\Console\Command;

class RefreshAllUserRelations extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'refresh:all_relations {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда обновляет счетчики всех отношений пользователей';

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		UserRelation::chunkById($this->argument('limit'),
			function ($items) {
				foreach ($items as $item) {
					echo('user_relation: ' . $item->id . "\n");

					$item->save();

					$first_user = $item->first_user()->any()->first();
					$second_user = $item->second_user()->any()->first();

					if (!empty($first_user)) {
						UpdateUserFriendsCount::dispatch($first_user);
						UpdateUserSubscribersCount::dispatch($first_user);
						UpdateUserSubscriptionsCount::dispatch($first_user);
						UpdateUserBlacklistsCount::dispatch($first_user);
					}

					if (!empty($second_user)) {
						UpdateUserFriendsCount::dispatch($second_user);
						UpdateUserSubscribersCount::dispatch($second_user);
						UpdateUserSubscriptionsCount::dispatch($second_user);
						UpdateUserBlacklistsCount::dispatch($second_user);
					}
				}
			});
	}
}
