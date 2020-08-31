<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class AttachUserGroupWithUserStatus extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:attach_group_with_status {name}';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Комманда присваивает группу к пользователям с определенным статусом, если такой статус есть у пользователя';
	protected $name;

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
		$this->name = $this->argument('name');

		User::any()
			->whereTextStatusLike($this->name)
			->chunkById(100, function ($items) {
				foreach ($items as $item) {

					$this->line('user: ' . $item->id . ' Статус: ' . $item->text_status);

					$this->item($item);
				}
			});
	}

	public function item(User $user)
	{
		if ($user->hasTextStatus($this->name)) {
			$user->attachUserGroupByNameIfExists($this->name);
			$user->save();
		}
	}
}
