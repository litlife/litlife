<?php

namespace Tests\Feature\User;

use App\User;
use Tests\TestCase;

class UserEditTest extends TestCase
{
	public function testEditProfileIsOkIfEditEmpty()
	{
		$user = User::factory()->create(['born_date' => null])->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->get(route('users.edit', ['user' => $user->id]))
			->assertOk();
	}

	public function testEditProfileUserWithSameNickExists()
	{
		$nick = $this->faker->userName;

		$nick_uppercase = mb_strtoupper($nick);
		$nick_lowercase = mb_strtolower($nick);

		$user = User::factory()->create([
				'nick' => $nick_lowercase
			]);
		$user->group->edit_profile = true;
		$user->push();

		$user_with_same_nick = User::factory()->create([
				'nick' => $nick_uppercase
			]);

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => mb_ucfirst($nick_lowercase),
					'first_name' => $user->first_name,
					'last_name' => $user->last_name,
					'name_show_type' => 'Nick'
				]
			)
			->assertSessionHasErrors(['nick' => __('validation.user_nick_unique')], null, 'user')
			->assertRedirect();
	}

	public function testEditProfileUserChangeNickCaseNoErrors()
	{
		$nick = uniqid();

		$nick_uppercase = mb_strtoupper($nick);
		$nick_lowercase = mb_strtolower($nick);

		$user = User::factory()->create([
				'nick' => $nick_uppercase
			]);
		$user->group->edit_profile = true;
		$user->push();

		$this->assertEquals($user->nick, $nick_uppercase);

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => $nick_lowercase,
					'first_name' => $user->first_name,
					'last_name' => $user->last_name,
					'name_show_type' => 'Nick'
				]
			);
		if (!empty(session('errors'))) dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$this->assertEquals($user->nick, $nick_lowercase);
	}

	public function testEditProfileRightDate()
	{
		$user = User::factory()->create()->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => uniqid(),
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'name_show_type' => 'Nick',
					'born_day' => '12',
					'born_month' => '02',
					'born_year' => '1993'
				]
			);
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$user->refresh();

		$this->assertEquals('1993-02-12', $user->born_date->format('Y-m-d'));
	}

	public function testEditProfileWrongDate()
	{
		$user = User::factory()->create()->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => uniqid(),
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'name_show_type' => 'Nick',
					'born_day' => '31',
					'born_month' => '02',
					'born_year' => '1993'
				]
			);
		//if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertSessionHasErrorsIn('user', ['born_date' => __('validation.date', ['attribute' => __('user.born_date')])])
			->assertRedirect();
	}

	public function testEditProfileEmptyDate()
	{
		$user = User::factory()->create()->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => uniqid(),
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'name_show_type' => 'Nick',
					//'born_day' => '31',
					//'born_month' => '02',
					//'born_year' => '1993'
				]
			);
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();
	}

	public function testEditProfileDayEmpty()
	{
		$user = User::factory()->create()->fresh();
		$user->group->edit_profile = true;
		$user->push();

		$response = $this->actingAs($user)
			->patch(route('users.update', ['user' => $user->id]),
				[
					'nick' => uniqid(),
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'name_show_type' => 'Nick',
					//'born_day' => '31',
					'born_month' => '02',
					'born_year' => '1993'
				]
			);
		if (!empty(session('errors'))) var_dump(session('errors'));
		$response->assertSessionHasErrorsIn('user', [
			'born_day' => __('validation.required_with', [
				'attribute' => __('user.born_day'), 'values' => __('user.born_month') . ' / ' . __('user.born_year')
			])
		])->assertRedirect();
	}
}
