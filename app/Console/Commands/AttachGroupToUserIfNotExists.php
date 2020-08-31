<?php

namespace App\Console\Commands;

use App\Enums\UserGroupEnum;
use App\User;
use App\UserGroup;
use Illuminate\Console\Command;

class AttachGroupToUserIfNotExists extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:attach_group_if_not_exists {limit=1000} {latest_id=0}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда присваивает группу к пользователям у которых нет ни одной группы';

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

		User::any()
			->where('id', '>=', $this->argument('latest_id'))
			->chunkById($this->argument('limit'), function ($items) {
				foreach ($items as $item) {

					$this->line('user: ' . $item->id);

					$this->item($item);
				}
			});
	}

	public function item($user)
	{
		if ($user->groups()->disableCache()->get()->isEmpty()) {
			$group = UserGroup::where('key', UserGroupEnum::User)->firstOrFail();

			$user->groups()->attach($group);
		}
	}
}
