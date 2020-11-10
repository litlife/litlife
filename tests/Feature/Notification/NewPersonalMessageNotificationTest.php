<?php

namespace Tests\Feature\Notification;

use App\Message;
use App\Notifications\NewPersonalMessageNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewPersonalMessageNotificationTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		Notification::fake();
	}

	public function testViaMailIfEnabled()
	{
		$recepient = User::factory()->with_confirmed_email()->create();
		$recepient->email_notification_setting->private_message = true;
		$recepient->email_notification_setting->save();

		$message = Message::factory()->create([
				'recepient_id' => $recepient->id
			]);

		$notification = new NewPersonalMessageNotification($recepient);

		$this->assertEquals(['mail'], $notification->via($recepient));
	}

	public function testViaEmptyIfDisabled()
	{
		$recepient = User::factory()->with_confirmed_email()->create();
		$recepient->email_notification_setting->private_message = false;
		$recepient->email_notification_setting->save();

		$message = Message::factory()->create([
				'recepient_id' => $recepient->id
			]);

		$notification = new NewPersonalMessageNotification($recepient);

		$this->assertEquals([], $notification->via($recepient));
	}

	public function testToMail()
	{
		$recepient = User::factory()->create();

		$message = Message::factory()->create([
				'recepient_id' => $recepient->id
			]);

		$notification = new NewPersonalMessageNotification($message);

		$mail = $notification->toMail($recepient);

		$this->assertEquals(__('notification.new_personal_message.subject'), $mail->subject);
		$this->assertEquals(__('notification.new_personal_message.line', ['userName' => $message->create_user->userName]), $mail->introLines[0]);

		$this->assertEquals(route('users.messages.index', ['user' => $message->create_user]), $mail->actionUrl);
		$this->assertEquals(__('notification.new_personal_message.action'), $mail->actionText);
	}
}
