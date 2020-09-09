<?php

namespace Tests\Feature\Job;

use App\Jobs\CreateSiteAccountIfNotExists;
use App\User;
use Tests\TestCase;

class CreateSiteAccountIfNotExistsJobTest extends TestCase
{
	public function testCreateSiteAccount()
	{
		$user = User::find(config('app.user_id'));
		$user->forceDelete();

		dispatch(new CreateSiteAccountIfNotExists());

		$user = User::find(config('app.user_id'));

		$this->assertNotNull($user);

		foreach ($user->email_notification_setting->getFillableAll() as $name => $value) {
			$this->assertFalse($value);
		}
	}
}
