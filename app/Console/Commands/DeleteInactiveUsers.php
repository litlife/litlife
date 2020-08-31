<?php

namespace App\Console\Commands;

use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class DeleteInactiveUsers extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'users:delete_inactive {limit=1000}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Удаляет неактивных пользователей';

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$limit = $this->argument('limit');

		$users = User::join('user_datas', 'users.id', '=', 'user_datas.user_id')
			// удаляем пользователей которые не заходили в течениe 3 лет
			->where('last_activity_at', '<', Carbon::now()->subYears(3)->toDateTimeString())
			->where('comment_count', '<', '1')
			->where('forum_message_count', '<', '1')
			->whereEmpty('favorite_music')
			->whereEmpty('favorite_authors')
			->whereEmpty('i_love')
			->whereEmpty('i_hate')
			->whereEmpty('about_self')
			->whereEmpty('favorite_quote')
			->where('photos_count', '<', '1')
			->active()
			->limit($limit)->get();

		foreach ($users as $user) {
			$user->suspend();
		}

		$users = User::join('user_datas', 'users.id', '=', 'user_datas.user_id')
			// которые не заходили больше месяца
			->where('last_activity_at', '<', Carbon::now()->subMonths(1)->toDateTimeString())
			// не провели на сайте больше дня
			->whereRaw('(("created_at" + \'1 day\'::interval) > "last_activity_at")')
			->where('comment_count', '<', '1')
			->where('forum_message_count', '<', '1')
			->whereEmpty('favorite_music')
			->whereEmpty('favorite_authors')
			->whereEmpty('i_love')
			->whereEmpty('i_hate')
			->whereEmpty('about_self')
			->whereEmpty('favorite_quote')
			->where('photos_count', '<', '1')
			->active()
			->limit($limit)->get();

		foreach ($users as $user) {
			$user->suspend();
		}

		User::cachedCountRefresh();
	}
}
