<?php

namespace Tests\Browser;

use App\User;
use App\UserEmail;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
	public function testExample()
	{
		$password = uniqid();

		$user = factory(User::class)->create([
			'password' => $password,
		]);

		$email = factory(UserEmail::class)->create([
			'user_id' => $user->id,
			'confirm' => true,
			'rescue' => true,
			'notice' => true,
			'show_in_profile' => true
		]);

		$this->browse(function ($browser) use ($user, $email, $password) {
			$browser->visit(route('home'))
				->type('login', $email->email)
				->type('login_password', $password)
				->press(__('auth.enter'))
				->assertSee($user->nick)
				->assertAuthenticated();
		});
	}

	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	protected function setUp(): void
	{
		parent::setUp();

		foreach (static::$browsers as $browser) {
			$browser->driver->manage()->deleteAllCookies();
		}
	}
	/*
		public function testRememberMe()
		{
			$password = uniqid();

			$user = factory(User::class)->create([
				'password' => $password,
			]);

			$email = factory(UserEmail::class)->create([
				'user_id' => $user->id,
				'confirm' => true,
				'rescue' => true,
				'notice' => true,
				'show_in_profile' => true
			]);

			$this->browse(function ($browser) use ($user, $email, $password) {

				$browser->visit(route('home'))
					->type('login', $email->email)
					->type('login_password', $password)
					->press(__('auth.enter'))
					->assertSee($user->nick);

				$this->assertNull($user->fresh()->getRememberToken());

				$browser->driver->manage()->deleteCookieNamed(config('session.cookie'));

				$browser->visit(route('home'))
					->assertDontSee($user->nick);

				$browser->visit(route('home'))
					->type('login', $email->email)
					->type('login_password', $password)
					->check('remember')
					->press(__('auth.enter'))
					->assertSee($user->nick);

				$this->assertNotNull($user->fresh()->getRememberToken());

				$browser->visit(route('home'))
					->assertSee($user->nick);

				$browser->driver->manage()->deleteCookieNamed(config('session.cookie'));

				dump($browser->driver->manage()->getCookies());

				$browser->visit(route('home'))
					->assertSee($user->nick);
			});


		}
		*/
}
