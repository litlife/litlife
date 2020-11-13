<?php

namespace Tests\Feature\Mailing;

use App\Mailing;
use App\Notifications\InvitationToSellBooksNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MailingArtisanCommandTest extends TestCase
{
    public function testSent()
    {
        Notification::fake();

        $mailing = Mailing::factory()->create();

        $this->assertFalse($mailing->isSent());

        Artisan::call('mailing:invitation_to_sell_books', ['latest_id' => $mailing->id]);

        $mailing->refresh();

        $this->assertTrue($mailing->isSent());

        Notification::assertSentTo(
            new AnonymousNotifiable,
            InvitationToSellBooksNotification::class,
            function ($notification, $channels, $notifiable) use ($mailing) {

                $this->assertContains('mail', $channels);

                $mail = $notification->toMail();

                $this->assertEquals($notifiable->routes['mail'], $mailing->email);

                $this->assertEquals(__('notification.invitation_to_sell_books.subject'), $mail->subject);
                $this->assertEquals(__('notification.invitation_to_sell_books.greeting').'!', $mail->greeting);
                //$this->assertEquals(__('notification.invitation_to_sell_books.line'), $mail->introLines[0]);
                $this->assertEquals(__('notification.invitation_to_sell_books.action'), $mail->actionText);
                $this->assertEquals(route('authors.how_to_start_selling_books'), $mail->actionUrl);

                return $notifiable->routes['mail'] === $mailing->email;
            }
        );
    }

    public function testDontSentIfAlreadySended()
    {
        Notification::fake();

        $mailing = Mailing::factory()->create(['sent_at' => now()]);

        $this->assertTrue($mailing->isSent());

        Artisan::call('mailing:invitation_to_sell_books', ['latest_id' => $mailing->id]);

        $mailing->refresh();

        Notification::assertNothingSent();
    }
}
