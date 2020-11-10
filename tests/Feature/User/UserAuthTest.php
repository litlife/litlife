<?php

namespace Tests\Feature\User;

use App\Http\Middleware\RefreshUserLastActivity;
use App\Http\Middleware\RemeberSessionGeoIpAndBrowser;
use App\User;
use Tests\TestCase;

class UserAuthTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testIfSuspendedHttp()
	{
		$this->withMiddleware(RefreshUserLastActivity::class);
		$this->withMiddleware(RemeberSessionGeoIpAndBrowser::class);

		$this->assertGuest();

		$password = uniqid();

		$user = User::factory()->with_confirmed_email()->create(
			)
			->fresh();
		$email = $user->emails()->first();

		$user->suspend();
		$user->save();
		$user->refresh();

		$response = $this->followingRedirects()
			->post(route('login'), [
				'login' => $email->email,
				'login_password' => $password
			]);
		$response->assertSeeText(__('auth.you_account_suspended_try_recover_password'))
			->assertSeeText(__('auth.go_to_recover_password'));

		$user->unsuspend();
		$user->save();
		$user->refresh();

		$response = $this->followingRedirects()
			->post(route('login'), [
				'login' => $email->email,
				'login_password' => $password
			]);
		$response->assertDontSeeText(__('auth.you_account_suspended_try_recover_password'))
			->assertDontSeeText(__('auth.go_to_recover_password'));
	}

	public function testPasswordRequestOk()
	{
		$email = uniqid();

		$this->get(route('password.request', [
			'email' => $email
		]))->assertOk();

		$email = uniqid() . '@' . uniqid() . '.com';

		$this->get(route('password.request', [
			'email' => $email
		]))->assertOk();
	}

	public function testShowLoginFormOk()
	{
		$this->get(route('login'))
			->assertOk();
	}
}
