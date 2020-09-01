<?php

namespace Tests\Feature\User;

use App\User;
use App\UserEmail;
use Carbon\Carbon;
use Illuminate\Auth\Events\Lockout;
use Tests\TestCase;

class UserLoginTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testLoginHttp()
	{
		$this->assertGuest();

		$password = $this->faker->password;

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password]);

		$email = $user->emails->first()->email;

		$response = $this->post(route('login'), [
			'login' => $email,
			'login_password' => $password
		]);
		if (!empty(session('errors'))) dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertAuthenticatedAs($user);
	}

	public function testLogoutHttp()
	{
		$user = factory(User::class)->create();

		$this->actingAs($user)
			->get(route('profile', $user))
			->assertOk();

		$this->assertAuthenticatedAs($user);

		$this->get(route('logout'))
			->assertRedirect();

		$this->assertGuest();
	}

	public function testRememberLoginHttp()
	{
		$this->assertGuest();

		$password = $this->faker->password;

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password]);

		$this->assertNull($user->token);

		$email = $user->emails->first()->email;

		$this->post(route('login'), [
			'login' => $email,
			'login_password' => $password,
			'remember' => 'on'
		])->assertSessionHasNoErrors()
			->assertRedirect();

		$response = $this->get(route('home'))
			->assertOk();

		$this->assertAuthenticatedAs(auth()->user());

		$user->refresh();

		$this->assertNotNull($user->token);

		$response->assertCookie(auth()->guard()->getRecallerName(), vsprintf('%s|%s|%s', [
			auth()->user()->id,
			auth()->user()->getRememberToken(),
			auth()->user()->password,
		]));
	}

	public function testLoginWithIdHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->create(['password' => $password])
			->fresh();

		$user->setting->login_with_id = true;
		$user->push();

		$this->post(route('login'),
			[
				'login' => (string)$user->id,
				'login_password' => $password
			])
			->assertSessionHasNoErrors();

		$this->assertAuthenticatedAs($user);
	}

	public function testLoginWithDisabledLoginWithIdHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->create(['password' => $password])
			->fresh();

		$user->setting->login_with_id = false;
		$user->push();

		$response = $this->post(route('login'),
			[
				'login' => (string)$user->id,
				'login_password' => $password
			]);

		$response->assertSessionHasErrorsIn('login',
			['login' => __('user_email.user_with_id_not_found')]);

		//$this->assertAuthenticatedAs($user);
	}

	public function testLoginWithIdAndEmailIsNotConfirmedHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->states('with_not_confirmed_email')
			->create(['password' => $password])
			->fresh();

		$user->setting->login_with_id = true;
		$user->push();

		$email = $user->emails->first();

		$this->post(route('login'),
			[
				'login' => (string)$user->id,
				'login_password' => $password
			])
			->assertSessionHasNoErrors();

		$this->assertAuthenticatedAs($user);

		//$this->assertAuthenticatedAs($user);
	}

	public function testLoginWithIdAndWrongPasswordHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->create(['password' => $password])
			->fresh();

		$user->setting->login_with_id = true;
		$user->push();

		$response = $this->post(route('login'),
			[
				'login' => (string)$user->id,
				'login_password' => 'wrong_password'
			]);

		//dump(session('errors'));

		$response->assertSessionHasErrorsIn('login',
			['login' => __('auth.failed')]);
	}

	public function testLoginWithConfirmedEmailHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password])
			->fresh();

		$email = $user->emails->first();

		$not_confirmed_email = factory(UserEmail::class)
			->create([
				'email' => $email->email,
				'confirm' => false
			]);

		$response = $this->post(route('login'),
			[
				'login' => (string)$email->email,
				'login_password' => $password
			]);

		//dump(session('errors'));

		$response->assertSessionHasNoErrors();

		$this->assertAuthenticatedAs($user);
	}

	public function testLoginWithConfirmedEmailAndWrongPasswordHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password])
			->fresh();

		$email = $user->emails->first();

		$not_confirmed_email = factory(UserEmail::class)
			->create([
				'email' => $email->email,
				'confirm' => false
			]);

		$response = $this->post(route('login'),
			[
				'login' => (string)$email->email,
				'login_password' => 'wrong password'
			]);

		//dump(session('errors'));

		$response->assertSessionHasErrorsIn('login',
			['login' => __('auth.failed')]);
	}

	public function testLoginWithUnconfirmedEmailHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->states('with_not_confirmed_email')
			->create(['password' => $password])
			->fresh();

		$email = $user->emails->first();

		$response = $this->post(route('login'),
			[
				'login' => (string)$email->email,
				'login_password' => $password
			]);

		//dump(session('errors'));

		$response->assertSessionHasErrorsIn('login',
			['login' => __('user_email.not_confirmed')])
			->assertRedirect(route('home'));

		$response = $this->followingRedirects()->post(route('login'),
			[
				'login' => (string)$email->email,
				'login_password' => $password
			]);

		$response->assertSeeText(__('user_email.not_confirm'));
	}

	public function testLoginIfDeletedAndRightPasswordHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password])
			->fresh();

		$email = $user->emails->first();

		$user->delete();

		$response = $this->post(route('login'),
			[
				'login' => (string)$email->email,
				'login_password' => $password
			]);

		//dump(session('errors'));

		$response->assertSessionHasErrorsIn('login', [
			'login' => __('user.deleted')
		])->assertRedirect(route('home'));
	}

	public function testLoginIfSuspendedAndRightPasswordHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password])
			->fresh();

		$email = $user->emails->first();

		$user->suspend();

		$response = $this->post(route('login'),
			[
				'login' => (string)$email->email,
				'login_password' => $password
			]);

		//dump(session('errors'));

		$response->assertSessionHasErrorsIn('login', [
			'login' => __('user.suspended')
		])->assertRedirect(route('home'));
	}

	public function testLoginIfSuspendedAndWrongPasswordHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password])
			->fresh();

		$email = $user->emails->first();

		$user->suspend();

		$response = $this->post(route('login'),
			[
				'login' => (string)$email->email,
				'login_password' => 'wrong_password'
			]);

		//dump(session('errors'));

		$response->assertSessionHasErrorsIn('login', [
			'login' => __('user.suspended')
		])->assertRedirect(route('home'));
	}

	public function testLoginIfForceDeletedAndRightPasswordHttp()
	{
		$password = uniqid();

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password])
			->fresh();

		$email = $user->emails->first();

		$user->forceDelete();

		$response = $this->post(route('login'),
			[
				'login' => (string)$email->email,
				'login_password' => $password
			]);

		//dump(session('errors'));

		$response->assertSessionHasErrorsIn('login', [
			'login' => __('user.deleted')
		])->assertRedirect(route('home'));
	}

	public function testLoginIfEmailInvaliFormatHttp()
	{
		$this->assertGuest();

		$password = uniqid();

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password]);

		$email = $user->emails->first();

		$response = $this->post(route('login'),
			[
				'login' => (string)uniqid(),
				'login_password' => $password
			])->assertRedirect();

		if (!empty(session('errors'))) var_dump(session('errors'));

		$response->assertSessionHasErrorsIn('login',
			['login' => __('user_email.nothing_found')]);
	}

	/*
	   public function testLoginRememberMe()
	   {

		   $password = uniqid();

		   $user = factory(User::class)
			   ->create(['password' => $password] )
			   ->fresh();

		   $email = $user->emails->first();
		   $email->confirm = true;
		   $email->save();

		   $response = $this->post(route('login'),
			   [
				   'login' => (string)$email->email,
				   'login_password' => $password,
				   'login_remember' => 'on'
			   ]);
		   //dump(session('errors'));

		   $response->assertSessionHasNoErrors();

		   $this->assertAuthenticatedAs($user);

		   $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
			   $user->id,
			   $user->getRememberToken(),
			   $user->password,
		   ]));

	   }
   */

	public function testThrottle()
	{
		$this->assertGuest();

		config(['auth.max_attempts' => 2]);
		config(['auth.decay_minutes' => 10]);

		$password = uniqid();

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password])
			->fresh();

		$email = $user->emails()->get()->first();

		for ($a = 0; $a < config('auth.max_attempts'); $a++) {

			$response = $this->post(route('login'), [
				'login' => $email->email,
				'login_password' => 'wrong_password'
			])->assertRedirect();

			$response->assertSessionHasErrorsIn('login', ['login' => __('auth.failed')]);
		}

		$this->expectsEvents(Lockout::class);

		$this->post(route('login'), [
			'login' => $email->email,
			'login_password' => 'wrong_password'
		])->assertRedirect()
			->assertSessionHasErrorsIn('login', [
				'login' => trans_choice('auth.throttle', config('auth.decay_minutes'), ['minutes' => config('auth.decay_minutes')])
			]);

		Carbon::setTestNow(now()->addMinutes(config('auth.decay_minutes'))->addMinute());

		$this->post(route('login'), [
			'login' => $email->email,
			'login_password' => 'wrong_password'
		])->assertRedirect()
			->assertSessionHasErrorsIn('login', ['login' => __('auth.failed')]);
	}

	public function test_allow_login_with_an_unconfirmed_mailbox_if_the_mailbox_is_added_before_moving_to_a_new_engine()
	{
		$this->assertGuest();

		$password = uniqid();

		$user = factory(User::class)
			->create(['password' => $password])
			->fresh();

		$not_confirmed_email = factory(UserEmail::class)
			->states('not_confirmed', 'created_before_move_to_new_engine')
			->create([
				'user_id' => $user->id,
			]);

		$this->assertTrue($not_confirmed_email->isCreatedBeforeMoveToNewEngine());

		$response = $this->post(route('login'),
			[
				'login' => (string)$not_confirmed_email->email,
				'login_password' => $password
			]);
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertSessionHasNoErrors();

		$this->assertAuthenticatedAs($user);
	}

	public function testIfEmptyPassword()
	{
		$this->assertGuest();

		$password = $this->faker->password;

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password]);

		$email = $user->emails->first()->email;

		$response = $this->post(route('login'), [
			'login' => $email,
			'login_password' => ''
		]);
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertRedirect()
			->assertSessionHasErrors(['login_password' => __('validation.required', ['attribute' => __('user.password')])],
				null, 'login');
	}

	public function testIfEmptyLogin()
	{
		$this->assertGuest();

		$password = $this->faker->password;

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password]);

		$email = $user->emails->first()->email;

		$response = $this->post(route('login'), [
			'login' => '',
			'login_password' => $password
		]);
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertRedirect()
			->assertSessionHasErrors(['login' => __('validation.required', ['attribute' => __('user.email')])],
				null, 'login');
	}

	public function testUpdateConfirmedMailboxCountOnLogin()
	{
		$this->assertGuest();

		$password = $this->faker->password;

		$user = factory(User::class)
			->states('with_confirmed_email')
			->create(['password' => $password]);

		$user->confirmed_mailbox_count = 0;
		$user->save();

		$email = $user->emails->first()->email;

		$response = $this->post(route('login'), [
			'login' => $email,
			'login_password' => $password
		]);
		if (!empty(session('errors'))) dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertAuthenticatedAs($user);

		$user->refresh();
		$this->assertEquals(1, $user->confirmed_mailbox_count);
	}

	public function testLoginWithIdWrongLoginHttp()
	{
		$this->post(route('login'),
			[
				'login' => '12.12',
				'login_password' => 'login_password'
			])
			->assertRedirect();
	}
}