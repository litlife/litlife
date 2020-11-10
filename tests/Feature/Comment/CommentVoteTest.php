<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\CommentVote;
use App\Notifications\CommentVoteNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class CommentVoteTest extends TestCase
{
	public function testVoteUp()
	{
		Notification::fake();

		$user = User::factory()->create();
		$user->group->comment_add_vote = true;
		$user->push();

		$comment = Comment::factory()->create();

		$ip = $this->faker->ipv4;

		$response = $this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '1']), ['REMOTE_ADDR' => $ip])
			->assertOk();

		$comment->refresh();

		$this->assertEquals(1, $comment->vote_up);
		$this->assertEquals(0, $comment->vote_down);
		$this->assertEquals(1, $comment->vote);

		$response->assertJson([
			'vote' => $comment->vote,
			'vote_up' => $comment->vote_up,
			'vote_down' => $comment->vote_down
		]);

		$comment_vote = $comment->votes()->first();

		$this->assertNotNull($comment_vote);
		$this->assertEquals($comment_vote->ip, $ip);

		$notifiable = $comment->create_user;

		Notification::assertNotSentTo($comment_vote->create_user, CommentVoteNotification::class);

		Notification::assertSentTo(
			$comment->create_user,
			CommentVoteNotification::class,
			function ($notification, $channels) use ($comment_vote, $notifiable) {
				$this->assertNotContains('mail', $channels);
				$this->assertContains('database', $channels);

				$data = $notification->toArray($notifiable);

				$this->assertEquals(__('notification.new_like_notification.comment.subject'), '');
				$this->assertEquals(__('notification.new_like_notification.comment.line', [
					'userName' => $comment_vote->create_user->userName,
					'book_title' => Str::limit($comment_vote->comment->commentable->title, 30)
				]), $data['description']);

				$this->assertEquals(route('comments.go', ['comment' => $comment_vote->comment]), $data['url']);

				return $notification->comment_vote->id == $comment_vote->id;
			}
		);
	}

	public function testVoteDown()
	{
		Notification::fake();

		$user = User::factory()->create();
		$user->group->comment_add_vote = true;
		$user->push();

		$comment = Comment::factory()->create();

		$response = $this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '-1']))
			->assertOk();

		$comment->refresh();

		$this->assertEquals(0, $comment->vote_up);
		$this->assertEquals(0, $comment->vote_down);
		$this->assertEquals(0, $comment->vote);

		$response->assertJson([
			'vote' => $comment->vote,
			'vote_up' => $comment->vote_up,
			'vote_down' => $comment->vote_down
		]);

		$comment_vote = $comment->votes()->first();

		$this->assertNotNull($comment_vote);
		$this->assertEquals(-1, $comment_vote->vote);
		$this->assertEquals($user->id, $comment_vote->create_user_id);
		$this->assertEquals($comment->id, $comment_vote->comment_id);

		Notification::assertNotSentTo($comment_vote->create_user, CommentVoteNotification::class);
		Notification::assertNotSentTo($comment->create_user, CommentVoteNotification::class);
	}

	public function testVoteUpVoteUp()
	{
		Notification::fake();

		$user = User::factory()->create();
		$user->group->comment_add_vote = true;
		$user->push();

		$comment = Comment::factory()->create();

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '1']))
			->assertOk();

		$comment->refresh();

		$this->assertEquals(1, $comment->vote_up);
		$this->assertEquals(0, $comment->vote_down);
		$this->assertEquals(1, $comment->vote);

		Notification::assertSentTo($comment->create_user, CommentVoteNotification::class);

		Notification::fake();

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '1']))
			->assertOk();

		$comment->refresh();

		$this->assertEquals(0, $comment->vote_up);
		$this->assertEquals(0, $comment->vote_down);
		$this->assertEquals(0, $comment->vote);

		Notification::assertNotSentTo($comment->create_user, CommentVoteNotification::class);
	}

	public function testVoteUpVoteDown()
	{
		Notification::fake();

		$user = User::factory()->create();
		$user->group->comment_add_vote = true;
		$user->push();

		$comment = Comment::factory()->create();

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '1']))
			->assertOk();

		$comment->refresh();

		$this->assertEquals(1, $comment->vote_up);
		$this->assertEquals(0, $comment->vote_down);
		$this->assertEquals(1, $comment->vote);

		Notification::assertSentTo($comment->create_user, CommentVoteNotification::class);

		Notification::fake();

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '-1']))
			->assertOk();

		$comment->refresh();

		$this->assertEquals(0, $comment->vote_up);
		$this->assertEquals(0, $comment->vote_down);
		$this->assertEquals(0, $comment->vote);

		Notification::assertNotSentTo($comment->create_user, CommentVoteNotification::class);
	}

	public function testVoteDownVoteUp()
	{
		Notification::fake();

		$user = User::factory()->create();
		$user->group->comment_add_vote = true;
		$user->push();

		$comment = Comment::factory()->create();

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '-1']))
			->assertOk();

		$comment->refresh();

		$this->assertEquals(0, $comment->vote_up);
		$this->assertEquals(0, $comment->vote_down);
		$this->assertEquals(0, $comment->vote);

		Notification::assertNotSentTo($comment->create_user, CommentVoteNotification::class);

		Notification::fake();

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '1']))
			->assertOk();

		$comment->refresh();

		$this->assertEquals(1, $comment->vote_up);
		$this->assertEquals(0, $comment->vote_down);
		$this->assertEquals(1, $comment->vote);

		Notification::assertSentTo($comment->create_user, CommentVoteNotification::class);
	}

	public function testVoteUpNotificationSettingDisable()
	{
		Notification::fake();

		$user = User::factory()->create();
		$user->group->comment_add_vote = true;
		$user->push();

		$comment = Comment::factory()->create();
		$comment->create_user->email_notification_setting->db_comment_vote_up = false;
		$comment->push();

		$this->assertFalse($comment->create_user->email_notification_setting->db_comment_vote_up);

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '1']))
			->assertOk();

		$comment->refresh();

		$comment_vote = $comment->votes()->first();

		Notification::assertSentTo(
			$comment->create_user,
			CommentVoteNotification::class,
			function ($notification, $channels) use ($comment_vote) {
				$this->assertNotContains('mail', $channels);
				$this->assertNotContains('database', $channels);

				return $notification->comment_vote->id == $comment_vote->id;
			}
		);
	}

	public function testVoteUpNotificationSettingEnable()
	{
		Notification::fake();

		$user = User::factory()->create();
		$user->group->comment_add_vote = true;
		$user->push();

		$comment = Comment::factory()->create();
		$comment->create_user->email_notification_setting->db_comment_vote_up = true;
		$comment->push();

		$this->assertTrue($comment->create_user->email_notification_setting->db_comment_vote_up);

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '1']))
			->assertOk();

		$comment->refresh();

		$comment_vote = $comment->votes()->first();

		Notification::assertSentTo(
			$comment->create_user,
			CommentVoteNotification::class,
			function ($notification, $channels) use ($comment_vote) {
				$this->assertNotContains('mail', $channels);
				$this->assertContains('database', $channels);

				return $notification->comment_vote->id == $comment_vote->id;
			}
		);
	}

	public function testComentVoteIfCreateUserDeleted()
	{
		Notification::fake();

		$user = User::factory()->create();
		$user->group->comment_add_vote = true;
		$user->push();

		$comment = Comment::factory()->create();
		$comment->create_user->forceDelete();
		$comment->refresh();

		$this->assertNull($comment->create_user);

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '1']))
			->assertForbidden();
	}

	public function testCanRemoveSelfVote()
	{
		$user = User::factory()->create();
		$user->group->comment_add_vote = true;
		$user->push();

		$comment = Comment::factory()->create();
		$comment->create_user()->associate($user);
		$comment->save();

		$vote = factory(CommentVote::class)->make(['vote' => 1]);
		$vote->create_user()->associate($user);
		$comment->votes()->save($vote);
		$comment->updateVotes();
		$comment->refresh();

		$this->assertEquals(1, $comment->votes()->count());
		$this->assertEquals(1, $comment->vote_up);

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '1']))
			->assertOk()
			->assertJson([
				'vote' => 0,
				'vote_up' => 0,
				'vote_down' => 0
			]);

		$comment->refresh();

		$this->assertEquals(1, $comment->votes()->count());
		$this->assertEquals(0, $comment->vote_up);
	}

	public function testCanSetLikeToSelfPostIfVoteRemovedExists()
	{
		$user = User::factory()->create();
		$user->group->comment_add_vote = true;
		$user->push();

		$comment = Comment::factory()->create();
		$comment->create_user()->associate($user);
		$comment->save();

		$vote = factory(CommentVote::class)->make(['vote' => 0]);
		$vote->create_user()->associate($user);
		$comment->votes()->save($vote);
		$comment->updateVotes();
		$comment->refresh();

		$this->assertEquals(1, $comment->votes()->count());
		$this->assertEquals(0, $comment->vote_up);

		$this->actingAs($user)
			->get(route('comments.vote', ['comment' => $comment, 'vote' => '1']))
			->assertOk()
			->assertJson([
				'vote' => 0,
				'vote_up' => 0,
				'vote_down' => 0
			]);

		$comment->refresh();

		$this->assertEquals(1, $comment->votes()->count());
		$this->assertEquals(0, $comment->vote_up);
	}

	public function testUpdateCommentVotesOnVoteDelete()
	{
		$vote = CommentVote::factory()->create(['vote' => 1]);

		$comment = $vote->comment;

		$this->assertEquals(1, $comment->vote);
		$this->assertEquals(1, $comment->vote_up);
		$this->assertEquals(0, $comment->vote_down);

		$vote->delete();

		$comment->refresh();

		$this->assertEquals(0, $comment->vote);
		$this->assertEquals(0, $comment->vote_up);
		$this->assertEquals(0, $comment->vote_down);
	}
}
