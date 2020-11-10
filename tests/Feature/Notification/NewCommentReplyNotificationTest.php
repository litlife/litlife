<?php

namespace Tests\Feature\Notification;

use App\Comment;
use App\Notifications\NewCommentReplyNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewCommentReplyNotificationTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();

		Notification::fake();
	}

	public function testViaMail()
	{
		$comment = Comment::factory()->create();

		$user = User::factory()->with_confirmed_email()->create();
		$user->email_notification_setting->comment_reply = true;
		$user->email_notification_setting->db_comment_reply = false;
		$user->push();

		$notification = new NewCommentReplyNotification($comment);

		$this->assertEquals(['mail'], $notification->via($user));
	}

	public function testViaDatabase()
	{
		$comment = Comment::factory()->create();

		$user = User::factory()->with_confirmed_email()->create();
		$user->email_notification_setting->comment_reply = false;
		$user->email_notification_setting->db_comment_reply = true;
		$user->push();

		$notification = new NewCommentReplyNotification($comment);

		$this->assertEquals(['database'], $notification->via($user));
	}

	public function testToArray()
	{
		$comment = Comment::factory()->create();

		$user = User::factory()->create();

		$notification = new NewCommentReplyNotification($comment);

		$data = $notification->toArray($user);

		$this->assertEquals(__('notification.comment_reply.subject'), $data['title']);
		$this->assertEquals(__('notification.comment_reply.line', ['userName' => $comment->create_user->userName]), $data['description']);
		$this->assertEquals(route('comments.go', ['comment' => $comment]), $data['url']);
	}

	public function testToMail()
	{
		$comment = Comment::factory()->create();

		$user = User::factory()->create();

		$notification = new NewCommentReplyNotification($comment);

		$mail = $notification->toMail($user);

		$this->assertEquals(__('notification.comment_reply.subject'), $mail->subject);
		$this->assertEquals(__('notification.comment_reply.line', ['userName' => $comment->create_user->userName]), $mail->introLines[0]);

		$this->assertEquals(route('comments.go', ['comment' => $comment]), $mail->actionUrl);
		$this->assertEquals(__('notification.comment_reply.action'), $mail->actionText);
	}
}
