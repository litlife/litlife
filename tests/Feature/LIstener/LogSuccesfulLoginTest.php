<?php

namespace Tests\Feature\Listener;

use App\User;
use Illuminate\Auth\Events\Login;
use Tests\TestCase;

class LogSuccesfulLoginTest extends TestCase
{
	public function test()
	{
		$user = factory(User::class)
			->create();
		$user->last_activity_at = null;
		$user->save();
		$user->refresh();

		$this->assertNull($user->last_activity_at);

		event(new Login('web', $user, true));

		$user->refresh();

		$log = $user->auth_logs()->first();

		$this->assertNotNull($log);

		$this->assertEquals($log->user_id, $user->id);
		$this->assertEquals($log->ip, '127.0.0.1');
		$this->assertTrue($log->is_remember_me_enabled);

		$this->assertTrue($this->app['session']->get('show_greeting'));

		$this->assertNotNull($user->last_activity_at);
	}
}
