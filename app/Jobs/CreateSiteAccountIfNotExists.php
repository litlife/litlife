<?php

namespace App\Jobs;

use App\User;
use Illuminate\Foundation\Bus\Dispatchable;

class CreateSiteAccountIfNotExists
{
	use Dispatchable;

	/**
	 * Create a new job instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Execute the job.
	 *
	 * @return void
	 */
	public function handle()
	{
		$user = User::find(config('app.user_id'));

		if (empty($user)) {
			$user = new User();
			$user->nick = config('app.name');
			$user->id = config('app.user_id');
			$user->email = config('app.email');
			$user->password = uniqid();
			$user->save();

			$user->setting->login_with_id = false;

			foreach ($user->email_notification_setting->getFillableAll() as $name => $value) {
				$user->email_notification_setting->$name = false;
			}

			$user->account_permissions->write_on_the_wall = 'me';
			$user->account_permissions->comment_on_the_wall = 'me';
			$user->account_permissions->write_private_messages = 'friends_and_subscribers';
			$user->account_permissions->view_relations = 'me';
			$user->push();
		}
	}
}
