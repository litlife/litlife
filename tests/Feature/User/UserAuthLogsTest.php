<?php

namespace Tests\Feature\User;

use App\User;
use App\UserAuthFail;
use App\UserAuthLog;
use Tests\TestCase;

class UserAuthLogsTest extends TestCase
{
	public function testSeeInProfileHttp()
	{
		$user = factory(User::class)
			->states('with_auth_log')
			->create()
			->fresh();

		$auth_log = $user->auth_logs()->first();

		$this->assertNotNull($auth_log);

		$this->actingAs($user)
			->get(route('profile', $user))
			->assertOk()
			->assertDontSeeText($auth_log->ip);

		$this->get(route('profile', $user))
			->assertOk()
			->assertDontSeeText($auth_log->ip);

		$admin = factory(User::class)
			->create()
			->fresh();
		$admin->group->display_technical_information = true;
		$admin->push();

		$this->actingAs($admin)
			->get(route('profile', $user))
			->assertOk()
			->assertSeeText($auth_log->ip);

		$this->actingAs($admin)
			->get(route('users.auth_logs', $user))
			->assertOk()
			->assertSeeText($auth_log->ip);

		$this->actingAs($user)
			->get(route('users.auth_logs', $user))
			->assertOk()
			->assertSeeText($auth_log->ip);

		$user2 = factory(User::class)
			->states('with_user_permissions')
			->create();

		$this->assertFalse($user2->can('watch_auth_logs', $user));

		$this->actingAs($user2)
			->get(route('users.auth_logs', $user))
			->assertForbidden()
			->assertDontSeeText($auth_log->ip);

		$this->actingAs($user2)
			->get(route('all_users_auth_logs', ['ip' => $auth_log->ip]))
			->assertForbidden();

		$this->actingAs($user)
			->get(route('all_users_auth_logs', ['ip' => $auth_log->ip]))
			->assertForbidden();

		$this->actingAs($admin)
			->get(route('all_users_auth_logs', ['ip' => $auth_log->ip]))
			->assertOk()
			->assertSeeText($auth_log->ip);
	}

	public function testSeeInProfileIfNotExistsHttp()
	{
		$user = factory(User::class)
			->create()
			->fresh();

		$this->actingAs($user)
			->get(route('profile', $user))
			->assertOk();
	}

	public function testIfUserAgentNotExists()
	{
		$user = factory(User::class)
			->create()
			->fresh();

		$log = factory(UserAuthLog::class)
			->states('without_user_agent')
			->create(['user_id' => $user->id]);

		$admin = factory(User::class)
			->create()
			->fresh();
		$admin->group->display_technical_information = true;
		$admin->push();

		$this->actingAs($admin)
			->get(route('users.auth_logs', $user))
			->assertOk()
			->assertSeeText($log->ip);

		$this->actingAs($admin)
			->get(route('all_users_auth_logs', ['ip' => $log->ip]))
			->assertOk()
			->assertSeeText($log->ip);
	}

	public function testUserAuthFailsRouteIsOk()
	{
		$user = factory(User::class)
			->create()
			->fresh();

		$fail = factory(UserAuthFail::class)
			->states('without_user_agent')
			->create(['user_id' => $user->id]);

		$admin = factory(User::class)
			->create()
			->fresh();
		$admin->group->display_technical_information = true;
		$admin->push();

		$this->actingAs($admin)
			->get(route('users.auth_fails', $user))
			->assertOk()
			->assertSeeText($fail->ip);
	}
}
