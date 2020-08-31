<?php

namespace App\Console\Commands;

use App\User;
use Illuminate\Console\Command;

class UsersRemoveAllPosts extends Command
{
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'user:remove_all_posts {user_id}';
	private $user;

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
		$this->user = User::any()->findOrFail($this->argument('user_id'));

		$this->posts();
		$this->blog_posts();
		$this->comments();
	}

	public function posts()
	{
		$this->user->posts()
			->chunkById(100, function ($items) {
				foreach ($items as $item) {
					$item->delete();
				}
			});
	}

	public function blog_posts()
	{
		$this->user->created_wall_posts()
			->chunkById(100, function ($items) {
				foreach ($items as $item) {
					$item->delete();
				}
			});
	}

	public function comments()
	{
		$this->user->comments()
			->chunkById(100, function ($items) {
				foreach ($items as $item) {
					$item->delete();
				}
			});
	}
}
