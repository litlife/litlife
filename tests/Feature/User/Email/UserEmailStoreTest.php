<?php

namespace Tests\Feature\User\Email;

use App\User;
use App\UserEmail;
use Tests\TestCase;

class UserEmailStoreTest extends TestCase
{
	public function testCreateHttp()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.emails.create', ['user' => $user]))
			->assertOk();
	}

	public function testStoreHttp()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$this->assertEquals(1, $user->emails()->count());

		$this->actingAs($user)
			->post(route('users.emails.store', ['user' => $user, 'email' => $this->faker->email]))
			->assertRedirect(route('users.emails.index', $user));

		$this->assertEquals(2, $user->emails()->count());

		$unconfirmed_email = $user->emails()->unconfirmed()->first();

		$this->assertNotNull($unconfirmed_email);
		$this->assertFalse($unconfirmed_email->isConfirmed());
		$this->assertTrue($unconfirmed_email->is_valid);
	}

	public function testIfSameEmailInOtherUserAccountExists()
	{
		$email = factory(UserEmail::class)
			->states('confirmed')
			->create();

		$user = factory(User::class)
			->states('without_email')
			->create();

		$response = $this->actingAs($user)
			->post(route('users.emails.store', ['user' => $user]), [
				'email' => $email->email
			])
			->assertRedirect();
		if (session('errors')) dump(session('errors'));
		$response->assertSessionHasNoErrors();

		$email->refresh();

		$this->assertTrue($email->isConfirmed());
		$this->assertEquals(1, $user->emails()->count());
		$this->assertEquals(1, $user->emails()->whereEmail($email->email)->count());

		$email2 = $user->emails()->whereEmail($email->email)->first();

		$this->assertFalse($email2->isConfirmed());
		$this->assertFalse($email2->isRescue());
		$this->assertFalse($email2->isNotice());
	}

	public function testIfEmailAlreadyAddedToUserAccount()
	{
		$user = factory(User::class)
			->states('with_confirmed_email')
			->create();

		$email = $user->emails()->first();

		$this->assertNotNull($email);

		$response = $this->actingAs($user)
			->post(route('users.emails.store', ['user' => $user]), [
				'email' => $email->email
			]);
		//dump(session('errors'));
		$this->assertContains(__('user_email.you_have_already_added_such_a_mailbox'), session('errors')->all());

		$response = $this->actingAs($user)
			->followingRedirects()
			->post(route('users.emails.store', ['user' => $user]), [
				'email' => $email->email
			])
			->assertOk()
			->assertSeeText(__('user_email.you_have_already_added_such_a_mailbox'));
	}
}
