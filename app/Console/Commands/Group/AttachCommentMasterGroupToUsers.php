<?php

namespace App\Console\Commands\Group;

use App\User;
use App\UserGroup;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AttachCommentMasterGroupToUsers extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:attach_comment_master_group_to_users {min_characters_count=1000} {min_comments_count=50} {id?}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда ищет пользователей, которые соответствуют критериям которым можно присвоить группу Мастер комментария';
	protected $group;
	protected $min_characters_count;
	protected $min_comments_count;

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	public function setGroup(UserGroup $group)
	{
		$this->group = $group;
	}

	/**
	 * Execute the console command.
	 *
	 * @return mixed
	 */
	public function handle()
	{
		$this->min_characters_count = $this->argument('min_characters_count');
		$this->min_comments_count = $this->argument('min_comments_count');

		$this->group = UserGroup::whereName('Мастер комментария')->first();

		if (empty($this->group))
			throw new Exception('Группа пользователей не найдена');

		User::when($this->argument('id'), function ($query) {
			$query->where('id', $this->argument('id'));
		})->whereHas('comments', function (Builder $query) {
			$query->select(DB::raw('"create_user_id", count(*) as "count"'))
				->where('characters_count', '>=', $this->min_characters_count)
				->groupBy('create_user_id')
				->havingRaw('count(*) >= ?', [$this->min_comments_count]);
		})->chunkById(100, function ($items) {
			foreach ($items as $item) {
				$this->line('Пользователь: ' . $item->id . '');

				DB::transaction(function () use ($item) {
					$this->attach($item);
				});
			}
		});
	}

	public function attach(User $user)
	{
		if (!$user->hasUserGroup($this->group)) {
			$this->line('Присоединяем группу ' . $this->group->name . ' к пользователю ' . $user->id . '');

			$user->attachUserGroup($this->group);
			$user->detachUserGroupByNameIfExists('Активный комментатор');
		}
	}
}
