<?php

namespace Tests\Feature\User;

use App\Invitation;
use Tests\TestCase;

class UserInvitationTest extends TestCase
{
	public function testInvitationFormHttp()
	{
		$this->get(route('invitation'))
			->assertOk();
	}

	public function testRightTokenHttp()
	{
		$invitation = factory(Invitation::class)
			->create();

		$this->get(route('users.registration', ['token' => $invitation->token]))
			->assertOk();
	}

	public function testWrongTokenHttp()
	{
		$this->get(route('users.registration', ['token' => '123']))
			->assertRedirect(route('invitation'));

		$errors = pos(session('errors')->getBag('invitation')->toArray());
		$this->assertContains(__('invitation.invitation_not_found_or_expired_please_send_a_new_invitation'), $errors);

		$this->followingRedirects()
			->get(route('users.registration', ['token' => '123']))
			->assertSeeText(__('invitation.invitation_not_found_or_expired_please_send_a_new_invitation'));
	}
}
