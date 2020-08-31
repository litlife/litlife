<?php

namespace App\Console\Commands\Group;

use App\Manager;
use App\UserGroup;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AttachAuthorGroupToUsers extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:attach_author_group_to_users {id?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда проверяет пользователей и присваивает группу автора пользователям у которых есть авторская страница';
	protected $group;

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
		$this->group = UserGroup::whereName('Автор')->first();

		if (empty($this->group))
			throw new Exception('Группа пользователей не найдена');

		Manager::authors()
			->accepted()
			->with('user', 'manageable')
			->when($this->argument('id'), function ($query) {
				$query->where('id', $this->argument('id'));
			})
			->chunkById(100, function ($items) {
				foreach ($items as $item) {

					$this->line('Manager: ' . $item->id . '');

					DB::transaction(function () use ($item) {
						$this->item($item);
					});
				}
			});
	}

	public function item(Manager $manager)
	{
		if (empty($manager->user))
			return false;

		if (!$manager->user->isAuthorGroupMustAttached())
			return false;

		if (!$manager->user->hasUserGroup($this->group)) {
			$this->line('Присоединяем группу Автор к пользователю ' . $manager->user->id . '');

			$manager->user->attachUserGroup($this->group);
		}
	}
}
