<?php

namespace Tests\Feature\Author\Manager;

use App\Manager;
use App\Notifications\AuthorManagerRejectedNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthorManagerRejectTest extends TestCase
{
    public function testRejectHttp()
    {
        Notification::fake();

        $admin = User::factory()->create();
        $admin->group->author_editor_check = true;
        $admin->push();

        $manager = Manager::factory()
            ->character_author()
            ->review_starts()
            ->create();
        $manager->status_changed_user_id = $admin->id;
        $manager->save();
        $manager->refresh();

        $this->assertTrue($manager->isReviewStarts());
        $this->assertEquals('author', $manager->character);

        $this->actingAs($admin)
            ->get(route('managers.decline', ['manager' => $manager->id]))
            ->assertRedirect(route('managers.on_check'))
            ->assertSessionHas(['success' => __('manager.declined')]);

        $manager->refresh();

        $this->assertTrue($manager->isRejected());

        $user = $manager->user;

        $this->assertNull($user->groups()->whereName('Автор')->first());

        Notification::assertSentTo(
            $manager->user,
            AuthorManagerRejectedNotification::class,
            function ($notification, $channels) use ($manager) {
                $this->assertContains('mail', $channels);
                $this->assertContains('database', $channels);

                $mail = $notification->toMail($manager->user);

                $this->assertEquals(__('notification.author_manager_request_rejected.subject'), $mail->subject);
                $this->assertEquals(__('notification.author_manager_request_rejected.line', ['author_name' => $manager->manageable->name]),
                    $mail->introLines[0]);
                $this->assertEquals(__('notification.author_manager_request_rejected.action'), $mail->actionText);
                $this->assertEquals(route('verifications.show', ['manager' => $manager]), $mail->actionUrl);

                $array = $notification->toArray($manager->user);

                $this->assertEquals(__('notification.author_manager_request_rejected.subject'), $array['title']);
                $this->assertEquals(__('notification.author_manager_request_rejected.line', ['author_name' => $manager->manageable->name]),
                    $array['description']);
                $this->assertEquals(route('verifications.show', ['manager' => $manager]), $array['url']);

                return $notification->manager->id == $manager->id;
            }
        );
    }
}
