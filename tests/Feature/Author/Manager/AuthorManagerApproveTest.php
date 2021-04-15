<?php

namespace Tests\Feature\Author\Manager;

use App\Author;
use App\Manager;
use App\Notifications\AuthorManagerAcceptedNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthorManagerApproveTest extends TestCase
{
    public function testAcceptHttp()
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
            ->get(route('managers.approve', ['manager' => $manager->id]))
            ->assertRedirect(route('managers.on_check'))
            ->assertSessionHas(['success' => __('manager.request_approved')]);

        $manager->refresh();

        $this->assertTrue($manager->isAccepted());

        Notification::assertSentTo(
            $manager->user,
            AuthorManagerAcceptedNotification::class,
            function ($notification, $channels) use ($manager) {
                $this->assertContains('mail', $channels);
                $this->assertContains('database', $channels);

                $mail = $notification->toMail($manager->user);

                $this->assertEquals(__('notification.author_manager_request_accepted.subject'), $mail->subject);
                $this->assertEquals(__('notification.author_manager_request_accepted.line', ['author_name' => $manager->manageable->name]),
                    $mail->introLines[0]);
                $this->assertEquals(__('notification.author_manager_request_accepted.action'), $mail->actionText);
                $this->assertEquals(route('authors.show', ['author' => $manager->manageable]), $mail->actionUrl);

                $array = $notification->toArray($manager->user);

                $this->assertEquals(__('notification.author_manager_request_accepted.subject'), $array['title']);
                $this->assertEquals(__('notification.author_manager_request_accepted.line', ['author_name' => $manager->manageable->name]),
                    $array['description']);
                $this->assertEquals(route('authors.show', ['author' => $manager->manageable]), $array['url']);

                return $notification->manager->id == $manager->id;
            }
        );
    }

    public function testAttachAuthorUserGroupOnApprove()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()
            ->character_author()
            ->review_starts()
            ->create();

        $manager->status_changed_user_id = $admin->id;
        $manager->save();
        $manager->refresh();

        $this->actingAs($admin)
            ->get(route('managers.approve', ['manager' => $manager->id]))
            ->assertRedirect(route('managers.on_check'))
            ->assertSessionHas(['success' => __('manager.request_approved')]);

        $manager->refresh();

        $this->assertTrue($manager->isAccepted());

        $user = $manager->user;

        $this->assertEquals('Автор', $user->groups()->disableCache()->whereName('Автор')->first()->name);
    }

    public function testDeleteOtherManagerRequestsFromSameUser()
    {
        $admin = User::factory()
            ->admin()
            ->create();

        $author = Author::factory()
            ->accepted()
            ->create();

        $user = Author::factory()
            ->create();

        $managerRequest = Manager::factory()
            ->character_author()
            ->review_starts()
            ->create([
                'status_changed_user_id' => $admin->id,
                'user_id' => $user->id,
                'manageable_id' => $author->id,
                'manageable_type' => 'author'
            ]);

        $managerRequest2 = Manager::factory()
            ->sent_for_review()
            ->review_starts()
            ->create([
                'user_id' => $user->id,
                'manageable_id' => $author->id,
                'manageable_type' => 'author'
            ]);

        $managerRequest3 = Manager::factory()
            ->character_author()
            ->private()
            ->create([
                'user_id' => $user->id,
                'manageable_id' => $author->id,
                'manageable_type' => 'author'
            ]);

        $managerRequest4 = Manager::factory()
            ->character_author()
            ->private()
            ->create([
                'manageable_id' => $author->id,
                'manageable_type' => 'author'
            ]);

        $this->actingAs($admin)
            ->get(route('managers.approve', ['manager' => $managerRequest->id]))
            ->assertRedirect(route('managers.on_check'))
            ->assertSessionHas(['success' => __('manager.request_approved')]);

        $managerRequest->refresh();
        $managerRequest2->refresh();
        $managerRequest3->refresh();
        $managerRequest4->refresh();

        $this->assertFalse($managerRequest->trashed());
        $this->assertTrue($managerRequest->isAccepted());
        $this->assertTrue($managerRequest2->trashed());
        $this->assertTrue($managerRequest3->trashed());

        $this->assertFalse($managerRequest4->trashed());
        $this->assertTrue($managerRequest4->isPrivate());
    }
}
