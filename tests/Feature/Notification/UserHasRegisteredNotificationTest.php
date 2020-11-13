<?php

namespace Tests\Feature\Notification;

use App\Notifications\UserHasRegisteredNotification;
use App\User;
use Tests\TestCase;

class UserHasRegisteredNotificationTest extends TestCase
{
    public function testVia()
    {
        $user = User::factory()->create();

        $notification = new UserHasRegisteredNotification($user);

        $this->assertEquals(['mail', 'database'], $notification->via($user));
    }

    public function testMail()
    {
        $user = User::factory()->with_confirmed_email()->create();

        $email = $user->emails()->first();

        $notification = new UserHasRegisteredNotification($user, __('password.your_entered_password'));

        $mail = $notification->toMail($user);

        $this->assertEquals(__('notification.user_has_registered.subject'), $mail->subject);

        $this->assertEquals(__('notification.user_has_registered.line'), $mail->introLines[0]);

        $this->assertGreaterThanOrEqual(4, count($mail->introLines));

        $this->assertEquals(__('notification.user_has_registered.line2'), $mail->introLines[1]);

        $this->assertEquals(__('notification.user_has_registered.line3', [
            'email' => $email->email
        ]), $mail->introLines[2]);

        $this->assertEquals(__('notification.user_has_registered.line4', [
            'password' => __('password.your_entered_password')
        ]), $mail->introLines[3]);

        $this->assertEquals(route('welcome'), $mail->actionUrl);
        $this->assertEquals(__('notification.user_has_registered.action'), $mail->actionText);
    }

    public function testPreviewRoute()
    {
        $user = User::factory()->with_confirmed_email()->create();

        $this->actingAs($user)
            ->get(route('preview.welcome_notification'))
            ->assertOk();
    }
}
