<?php

namespace Tests\Feature\User\Invitation;

use App\Enums\UserGroupEnum;
use App\Invitation;
use App\Notifications\UserHasRegisteredNotification;
use App\User;
use App\UserEmail;
use App\UserGroup;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserInvitationStoreUserTest extends TestCase
{
	public function getPassword()
	{
		return 'Pw1' . $this->faker->password(10);
	}

	public function testStoreNewUserIsOk()
	{
		Notification::fake();

		$invitation = factory(Invitation::class)
			->create();

		$password = $this->getPassword();
		$ip = $this->faker->ipv4;

		$user = factory(User::class)
			->make();

		$response = $this->withHeader('REMOTE_ADDR', $ip)
			->post(route('users.store', ['token' => $invitation->token]),
				[
					'nick' => $user->nick,
					'first_name' => $user->first_name,
					'last_name' => $user->last_name,
					'middle_name' => '',
					'gender' => 'male',
					'password' => $password,
					'password_confirmation' => $password,
					'name_show_type' => 'FullLastNameFirstName'
				]);

		if (session('errors'))
			var_dump(session('errors'));

		$response->assertSessionHasNoErrors()
			->assertRedirect(route('welcome'));

		$this->assertAuthenticated();

		$user = User::where('nick', $user->nick)->first();

		$this->assertNotNull($user);
		$this->assertAuthenticatedAs($user);
		$this->assertEquals(UserGroupEnum::User, $user->group->key);
		$this->assertEquals(UserGroup::where('key', UserGroupEnum::User)->firstOrFail()->id, $user->user_group_id);
		$this->assertEquals($ip, $user->reg_ip);
		$this->assertNotNull($user->last_activity_at);
		$this->assertGreaterThanOrEqual($user->created_at, $user->last_activity_at);

		$this->assertDatabaseMissing('user_settings', ['user_id' => $user->id]);

		$bookmark_folder = $user->bookmark_folders()->first();

		$this->assertNotNull($bookmark_folder);
		$this->assertEquals(__('bookmark_folder.default_title'), $bookmark_folder->title);
		$this->assertTrue(Cache::get('users_count_refresh'));

		$invitation->refresh();

		$this->assertTrue($invitation->trashed());

		$email = $user->emails()->first();

		$this->assertNotNull($email);
		$this->assertEquals($invitation->email, $email->email);
		$this->assertTrue($email->confirm);
		$this->assertTrue($email->rescue);
		$this->assertTrue($email->notice);

		Notification::assertSentTo(
			$user,
			UserHasRegisteredNotification::class,
			function ($notification, $channels) use ($user) {
				return $notification->user->id == $user->id;
			}
		);
	}

	public function testSameEmailUnconfirmedExists()
	{
		$email = factory(UserEmail::class)
			->states('confirmed')
			->create([
				'confirm' => false,
			]);

		$invitation = factory(Invitation::class)
			->create([
				'email' => $email->email,
			]);

		$this->assertEquals($email->email, $invitation->email);

		$password = $this->getPassword();

		$user = factory(User::class)
			->make();

		$response = $this->post(route('users.store', ['token' => $invitation->token]),
			[
				'nick' => $user->nick,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
				'middle_name' => $user->middle_name,
				'gender' => 'male',
				'password' => $password,
				'password_confirmation' => $password,
				'name_show_type' => 'FullLastNameFirstName'
			])
			->assertRedirect(route('invitation'))
			->assertSessionHasErrors('error', __('invitation.invitation_not_found_or_expired_please_send_a_new_invitation'), 'registration');
	}

	public function testStoreNewUserDate()
	{
		$invitation = factory(Invitation::class)
			->create();

		$password = 'Pw1' . $this->getPassword();

		$response = $this->post(route('users.store', ['token' => $invitation->token]),
			[
				'nick' => uniqid(),
				'first_name' => $this->faker->firstName,
				'last_name' => $this->faker->lastName,
				'middle_name' => '',
				'gender' => 'male',
				'password' => $password,
				'password_confirmation' => $password,
				'name_show_type' => 'FullLastNameFirstName',
				'born_day' => '12',
				'born_month' => '12',
				'born_year' => '1990'
			]);
		if (session('errors')) var_dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('welcome'));

		$user = auth()->user();

		$this->assertEquals('1990-12-12', $user->born_date->format('Y-m-d'));
	}

	public function testNewUserSameNickExists()
	{
		$nick = $this->faker->userName;

		$nick_uppercase = mb_strtoupper($nick);
		$nick_lowercase = mb_strtolower($nick);

		$invitation = factory(Invitation::class)
			->create();

		$user = factory(User::class)
			->create(['nick' => $nick_uppercase]);

		$user_filled_data = factory(User::class)
			->make(['nick' => $nick_lowercase]);

		$post = $user_filled_data->toArray();
		$password = $this->getPassword();
		$post['password'] = $password;
		$post['password_confirmation'] = $password;

		$this->post(route('users.store', ['token' => $invitation->token]), $post)
			->assertSessionHasErrors(['nick' => __('validation.user_nick_unique')], null, 'registration')
			->assertRedirect();
	}

	public function testNewUser()
	{
		$invitation = factory(Invitation::class)
			->create();

		$user_filled_data = factory(User::class)
			->make();

		$post = $user_filled_data->toArray();

		$password = $this->getPassword();
		$post['password'] = $password;
		$post['password_confirmation'] = $password;

		$response = $this->post(route('users.store', ['token' => $invitation->token]), $post)
			->assertRedirect();
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$user_email = UserEmail::email($invitation->email)
			->first();

		$user = $user_email->user;

		$this->assertNotNull($user);
		$this->assertNotNull($user_email->user->group);
		$this->assertEquals(UserGroup::where('key', UserGroupEnum::User)->firstOrFail()->id, $user_email->user->group->id);
	}

	public function testEmptyPassword()
	{
		$invitation = factory(Invitation::class)
			->create();

		$user_filled_data = factory(User::class)
			->make();

		$post = $user_filled_data->toArray();
		$password = $this->getPassword();
		$post['password'] = '';
		$post['password_confirmation'] = '';

		$response = $this->post(route('users.store', ['token' => $invitation->token]), $post)
			->assertRedirect();
		//dump(session('errors'));
		$response->assertSessionHasErrors(['password' => __('validation.required', ['attribute' => __('user.password')])],
			null, 'registration');
	}

	public function testFrequentPassword()
	{
		config(['auth.max_frequent_password_count' => 1]);

		$invitation = factory(Invitation::class)
			->create();

		$user_filled_data = factory(User::class)
			->make();

		$post = $user_filled_data->toArray();
		$password = $this->getPassword();
		$post['password'] = $password;
		$post['password_confirmation'] = $password;

		$user = factory(User::class, config('auth.max_frequent_password_count'))
			->create(['password' => $password]);

		$this->assertEquals(config('auth.max_frequent_password_count'), User::wherePassword($password)->count());

		$response = $this->post(route('users.store', ['token' => $invitation->token]), $post)
			->assertRedirect();
		//dump(session('errors'));
		$response->assertSessionHasErrors(['password' => __('password.frequent')],
			null, 'registration');
	}

	public function testIfInvitationDeleted()
	{
		$invitation = factory(Invitation::class)
			->create();

		$password = $this->getPassword();
		$ip = $this->faker->ipv4;

		$user = factory(User::class)
			->make();

		$invitation->delete();

		$response = $this->post(route('users.store', ['token' => $invitation->token]),
			[
				'nick' => $user->nick,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
				'middle_name' => '',
				'gender' => 'male',
				'password' => $password,
				'password_confirmation' => $password,
				'name_show_type' => 'FullLastNameFirstName'
			])
			->assertRedirect(route('invitation'))
			->assertSessionHasErrors('error', __('invitation.invitation_not_found_or_expired_please_send_a_new_invitation'), 'registration');
	}

	public function testIfSameEmailConfirmedExists()
	{
		$email = factory(UserEmail::class)
			->states('confirmed')
			->create();

		$invitation = factory(Invitation::class)
			->create(['email' => $email->email]);

		$password = $this->getPassword();
		$ip = $this->faker->ipv4;

		$user = factory(User::class)
			->make();

		$response = $this->post(route('users.store', ['token' => $invitation->token]),
			[
				'nick' => $user->nick,
				'first_name' => $user->first_name,
				'last_name' => $user->last_name,
				'middle_name' => '',
				'gender' => 'male',
				'password' => $password,
				'password_confirmation' => $password,
				'name_show_type' => 'FullLastNameFirstName'
			])
			->assertRedirect(route('invitation'))
			->assertSessionHasErrors('error', __('The user with this confirmed mailbox is already registered'), 'registration');
	}
}
