<?php

namespace Tests\Feature\User;

use App\User;
use App\UserEmail;
use App\UserGroup;
use Carbon\Carbon;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testOnlineFilter()
	{
		$user = User::factory()->create();

		$other_user = User::factory()->create();

		$now = now();

		$this->actingAs($user)
			->get(route('profile', ['user' => $user->id]))
			->assertOk();

		$this->actingAs($other_user)
			->get(route('users', ['search' => $user->userName, 'is_online' => 1]))
			->assertOk()
			->assertSeeText($user->userName);

		Carbon::setTestNow($now->addMinutes(config('litlife.user_last_activity'))->subSeconds(2));

		$this->actingAs($other_user)
			->get(route('users', ['search' => $user->userName, 'is_online' => 1]))
			->assertOk()
			->assertSeeText($user->userName);

		Carbon::setTestNow($now->addMinutes(config('litlife.user_last_activity'))->addSeconds(2));

		$this->actingAs($other_user)
			->get(route('users', ['search' => $user->userName, 'is_online' => 1]))
			->assertOk()
			->assertDontSeeText($user->userName);
	}

	public function testNameEmailAndEmailHttp()
	{
		$user = User::factory()->create();

		$email = $this->faker->email;

		$this->actingAs($user)
			->get(route('users', ['email' => $email, 'search' => $email]))
			->assertOK();
	}

	public function testGroup()
	{
		$user2 = User::factory()->create();

		$user = User::factory()->create();

		$group = UserGroup::factory()->create(['show' => true]);

		$user->groups()->syncWithoutDetaching([$group->id]);

		$this->actingAs($user2)
			->get(route('users', ['group' => $group->id]))
			->assertOK()
			->assertSeeText($user->nick);

		$user->groups()->detach([$group->id]);

		$this->actingAs($user2)
			->get(route('users', ['group' => $group->id]))
			->assertOK()
			->assertDontSeeText($user->nick);
	}

	public function testConfirmedEmail()
	{
		$admin = User::factory()->create();

		$email = UserEmail::factory()->confirmed()->create();

		$user = $email->user;

		$this->actingAs($admin)
			->get(route('users', ['email' => $email->email]))
			->assertOK()
			->assertDontSeeText(__('user.nothing_found'))
			->assertSeeText($user->nick);
	}

	public function testNotConfirmedEmail()
	{
		$admin = User::factory()->create();

		$email = UserEmail::factory()->not_confirmed()->create();

		$user = $email->user;

		$this->actingAs($admin)
			->get(route('users', ['email' => $email->email]))
			->assertOK()
			->assertSeeText(__('user.nothing_found'))
			->assertDontSeeText($user->nick);
	}

	public function testEscapeCharactersInEmail()
	{
		$user = User::factory()->create();

		$response = $this->actingAs($user)
			->get(route('users', ['email' => '\\']))
			->assertOk();
	}

	public function testEmail()
	{
		$email = UserEmail::factory()->confirmed()->create();

		$user = $email->user;

		$response = $this->actingAs($user)
			->get(route('users', ['email' => $email->email]))
			->assertOk()
			->assertDontSeeText(__('user.nothing_found'))
			->assertSeeText($user->nick);
	}

	public function testPerPage()
	{
		$user = User::factory()->create();

		$response = $this->actingAs($user)
			->get(route('users', ['per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['users']->perPage());
	}
}
