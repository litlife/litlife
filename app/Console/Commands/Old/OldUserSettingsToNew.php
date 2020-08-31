<?php

namespace App\Console\Commands\Old;

use App\User;
use Illuminate\Console\Command;

class OldUserSettingsToNew extends Command
{
	public $user;
	/**
	 * The name and signature of the console command.
	 *
	 * @var string
	 */
	protected $signature = 'to_new:user_settings';
	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = ' ';

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
		User::any()->chunk(1000, function ($users) {
			foreach ($users as $user) {

				$this->user = $user;

				echo($this->user->id . " \n");

				//$this->userAccesses();
				$this->genreBlacklist();
				//$this->user->setting->save();

			}
		});
	}

	public function genreBlacklist()
	{
		if (!empty($this->user->setting->genre_blacklist)) {
			$genre_blacklist = unserialize(trim($this->user->setting->genre_blacklist));

			if (count($genre_blacklist) > 0) {
				$this->user->genre_blacklist()->sync($genre_blacklist);
			}
		}
	}

	public function userAccesses()
	{
		$user_accesses = unserialize($this->user->setting->user_access);

		$choices = [
			'everyone',
			'friends',
			'me',
			'friends_and_subscribers'
		];

		if (isset($user_accesses[1])) {
			$user_accesses['write_on_the_wall'] = $choices[($user_accesses[1] - 1)];
			unset($user_accesses[1]);
		}

		if (isset($user_accesses[2])) {
			$user_accesses['comment_on_the_wall'] = $choices[($user_accesses[2] - 1)];
			unset($user_accesses[2]);
		}

		if (isset($user_accesses[3])) {
			$user_accesses['write_private_messages'] = $choices[($user_accesses[3] - 1)];
			unset($user_accesses[3]);
		}

		if (!$user_accesses)
			$user_accesses = null;

		$this->user->setting->permissions_to_act = $user_accesses;
	}
}
