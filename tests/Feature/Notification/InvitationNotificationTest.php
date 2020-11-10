<?php

namespace Tests\Feature\Notification;

use App\Invitation;
use App\Notifications\InvitationNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Tests\TestCase;

class InvitationNotificationTest extends TestCase
{
	public function testVia()
	{
		$invitation = Invitation::factory()->create();

		$notification = new InvitationNotification($invitation);

		$this->assertEquals(['mail'], $notification->via(new AnonymousNotifiable));
	}

	public function testMail()
	{
		$invitation = Invitation::factory()->create();

		$notification = new InvitationNotification($invitation);

		$mail = $notification->toMail(new AnonymousNotifiable);

		$this->assertEquals($notification->invitation->email, $invitation->email);
		$this->assertEquals($notification->invitation->token, $invitation->token);

		$this->assertEquals(__('notification.invitation.subject'), $mail->subject);
		$this->assertEquals(__('notification.invitation.line'), $mail->introLines[0]);
		$this->assertEquals(__('notification.invitation.action'), $mail->actionText);
		$this->assertEquals(route('users.registration', $invitation->token), $mail->actionUrl);
	}
}
