<?php

namespace Tests\Feature\User;

use App\Collection;
use App\Comment;
use App\Enums\UserSubscriptionsEventNotificationType;
use App\Notifications\NewCommentInCollectionNotification;
use App\User;
use App\UserSubscriptionsEventNotification;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UserSubscriptionsEventNotificationTest extends TestCase
{
    public function testToggle()
    {
        $collection = Collection::factory()->accepted()->create()
            ->fresh();

        $admin = User::factory()->create();

        $response = $this->actingAs($admin)
            ->get(route('collections.event_notification_subcriptions.toggle', $collection),
                ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonFragment(['status' => 'subscribed']);

        $subscription = $admin->eventNotificationSubscriptions()->first();

        $response->assertJsonFragment($subscription->toArray());

        $this->assertNotNull($subscription);
        $this->assertEquals($admin->id, $subscription->notifiable_user_id);
        $this->assertInstanceOf(Collection::class, $subscription->eventable);
        $this->assertEquals($collection->id, $subscription->eventable_id);
        $this->assertEquals(UserSubscriptionsEventNotificationType::NewComment, $subscription->event_type->value);
        $this->assertNotNull($subscription->created_at);
        $this->assertNotNull($subscription->updated_at);

        $this->actingAs($admin)
            ->get(route('collections.event_notification_subcriptions.toggle', $collection),
                ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk()
            ->assertJsonFragment(['status' => 'unsubscribed']);

        $subscription = $admin->eventNotificationSubscriptions()->first();

        $this->assertNull($subscription);
    }

    public function testSendNotificationToSubscrider()
    {
        Notification::fake();

        $user = User::factory()->admin()->create();

        $subscription = UserSubscriptionsEventNotification::factory()->collection()->new_comment()->create(['notifiable_user_id' => $user->id]);

        $collection = $subscription->eventable;
        $collection->who_can_comment = 'everyone';
        $collection->save();
        $collection->refresh();

        $commentator = User::factory()->admin()->create();

        $this->actingAs($commentator)
            ->post(route('comments.store', [
                'commentable_type' => '18',
                'commentable_id' => $collection->id
            ]), [
                'bb_text' => $this->faker->realText(200)
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $comment = $collection->comments()->first();

        $this->assertNotNull($comment);

        Notification::assertNotSentTo($collection->create_user, NewCommentInCollectionNotification::class);
        Notification::assertNotSentTo($commentator, NewCommentInCollectionNotification::class);

        $this->assertNewCommentInCollectionNotificationSent($user, $collection, $comment);
    }

    public function assertNewCommentInCollectionNotificationSent(User $user, Collection $collection, Comment $comment)
    {
        Notification::assertSentTo(
            $user,
            NewCommentInCollectionNotification::class,
            function ($notification, $channels) use ($collection, $user, $comment) {
                $this->assertContains('mail', $channels);
                $this->assertContains('database', $channels);

                $data = $notification->toArray($user);

                $this->assertEquals(__('notification.new_comment_in_collection.subject'), $data['title']);
                $this->assertEquals(__('notification.new_comment_in_collection.line', [
                    'create_user_name' => $comment->create_user->userName,
                    'collection_title' => $collection->title
                ]), $data['description']);

                $this->assertEquals(route('comments.go', ['comment' => $comment]), $data['url']);

                ///

                $mail = $notification->toMail($user);

                $this->assertEquals(__('notification.new_comment_in_collection.subject'), $mail->subject);
                $this->assertEquals(__('notification.new_comment_in_collection.subject'), $mail->subject);

                $this->assertEquals(__('notification.new_comment_in_collection.line', [
                    'create_user_name' => $comment->create_user->userName,
                    'collection_title' => $collection->title
                ]), $mail->introLines[0]);

                $this->assertEquals(__('notification.new_comment_in_collection.action'), $mail->actionText);
                $this->assertEquals(route('comments.go', ['comment' => $comment]), $mail->actionUrl);

                $this->assertEquals('<a href="'.route('collections.event_notification_subcriptions.toggle', $collection).'">'.
                    __('collection.unsubscribe_from_notifications').'</a>',
                    $mail->outroLines[0]);

                return $notification->comment->id == $comment->id;
            }
        );
    }

    public function testDontSendNotificationToParentComment()
    {
        Notification::fake();

        $subscriber_user = User::factory()->admin()->create();

        $parent_comment = Comment::factory()
            ->collection()
            ->create([
                'create_user_id' => $subscriber_user->id
            ]);

        $collection = $parent_comment->commentable;

        $subscription = UserSubscriptionsEventNotification::factory()
            ->collection()
            ->new_comment()
            ->create([
                'eventable_id' => $collection->id,
                'notifiable_user_id' => $subscriber_user->id
            ]);

        $commentator = User::factory()->admin()->create();

        $collection->who_can_comment = 'everyone';
        $collection->save();
        $collection->refresh();

        $this->actingAs($commentator)
            ->post(route('comments.store', [
                'commentable_type' => '18',
                'commentable_id' => $collection->id,
                'parent' => $parent_comment->id
            ]), [
                'bb_text' => $this->faker->realText(200)
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $collection->refresh();

        $this->assertEquals(2, $collection->comments_count);

        $comment = $collection->comments()->orderBy('id', 'desc')->first();

        $this->assertNotNull($comment);

        Notification::assertNotSentTo($subscriber_user, NewCommentInCollectionNotification::class);
    }
}
