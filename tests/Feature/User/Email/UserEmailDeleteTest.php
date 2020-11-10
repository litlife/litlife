<?php

namespace Tests\Feature\User\Email;

use App\User;
use App\UserEmail;
use Tests\TestCase;

class UserEmailDeleteTest extends TestCase
{
	public function testDelete()
	{
		$user = User::factory()->with_confirmed_email()->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());

		$email2 = UserEmail::factory()->confirmed()->noticed()->rescued()->create(['user_id' => $user->id]);

		$this->assertEquals(2, $user->emails()->count());

		$this->actingAs($user)
			->delete(route('users.emails.destroy', ['user' => $user, 'email' => $email->id]))
			->assertRedirect(route('users.emails.index', $user));

		$this->assertEquals(1, $user->emails()->count());
	}

	public function testErrorAtLeastOneEmailMustBeConfirmed()
	{
		$user = User::factory()->with_confirmed_email()->create();

		$this->assertEquals(1, $user->emails()->count());

		$this->actingAs($user)
			->followingRedirects()
			->delete(route('users.emails.destroy', ['user' => $user, 'email' => $user->emails()->first()->id]))
			->assertSeeText(__('user_email.error_1'));
	}

	public function testErrorAtLeastOneEmailMustBeForResue()
	{
		$user = User::factory()->with_confirmed_email()->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());

		$email2 = UserEmail::factory()->confirmed()->noticed()->not_rescued()->create(['user_id' => $user->id]);

		$this->actingAs($user)
			->followingRedirects()
			->delete(route('users.emails.destroy', ['user' => $user, 'email' => $email->id]))
			->assertSeeText(__('user_email.error_2'));
	}

	public function testErrorAtLeastOneEmailMustBeForNotice()
	{
		$user = User::factory()->with_confirmed_email()->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());
		$this->assertTrue($email->isConfirmed());
		$this->assertTrue($email->isNotice());

		$email2 = UserEmail::factory()->confirmed()->rescued()->not_noticed()->create(['user_id' => $user->id]);

		$this->assertEquals(2, $user->emails()->count());

		$this->actingAs($user)
			->followingRedirects()
			->delete(route('users.emails.destroy', ['user' => $user, 'email' => $email->id]))
			->assertSeeText(__('user_email.you_must_have_at_least_one_mailbox_to_send_notifications'));

		$this->assertEquals(2, $user->emails()->count());
	}

	public function testNotConfirmedIfConfirmedExists()
	{
		$user = User::factory()->with_confirmed_email()->create();

		$email = $user->emails()->first();

		$this->assertTrue($email->isRescue());
		$this->assertTrue($email->isConfirmed());
		$this->assertTrue($email->isNotice());

		$email2 = UserEmail::factory()->not_confirmed()->not_rescued()->not_noticed()->create(['user_id' => $user->id]);

		$this->assertEquals(2, $user->emails()->count());

		$this->actingAs($user)
			->delete(route('users.emails.destroy', ['user' => $user, 'email' => $email2->id]))
			->assertSessionHasNoErrors()
			->assertRedirect(route('users.emails.index', $user));

		$this->assertEquals(1, $user->emails()->count());
	}
}
