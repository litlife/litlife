<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class CommentUpdatePolicyTest extends TestCase
{
	public function testAlwaysCanUpdateIfCommentPrivate()
	{
		$comment = Comment::factory()->private()->create();

		$user = $comment->create_user;

		$this->assertTrue($user->can('update', $comment));
	}

	public function testUpdatePermissions()
	{
		$user = User::factory()->with_user_group()->create();
		$admin = User::factory()->with_user_group()->create();

		$comment = Comment::factory()->create(['create_user_id' => $user->id]);

		$this->assertFalse($user->can('update', $comment));
		$this->assertFalse($admin->can('update', $comment));

		$user->group->comment_edit_my = true;
		$user->push();
		$user->refresh();
		$admin->group->comment_edit_my = true;
		$admin->push();
		$admin->refresh();

		$this->assertTrue($user->can('update', $comment));
		$this->assertFalse($admin->can('update', $comment));

		$admin->group->comment_edit_other_user = true;
		$admin->push();
		$admin->refresh();

		$this->assertTrue($admin->can('update', $comment));

		$user->group->comment_edit_my = false;
		$user->group->comment_self_edit_only_time = true;
		$user->push();
		$user->refresh();
		$admin->group->comment_edit_other_user = false;
		$admin->group->comment_self_edit_only_time = true;
		$admin->push();
		$admin->refresh();

		Carbon::setTestNow(now()->addSeconds(604800)->subSeconds(100));

		$this->assertTrue($user->can('update', $comment));
		$this->assertFalse($admin->can('update', $comment));

		Carbon::setTestNow(now()->addSeconds(604800)->addSeconds(100));

		$this->assertFalse($user->can('update', $comment));
		$this->assertFalse($admin->can('update', $comment));
	}

	public function testUpdatePolicy()
	{
		$user = User::factory()->create();
		$user->group->comment_edit_my = false;
		$user->group->comment_self_edit_only_time = true;
		$user->group->comment_edit_other_user = false;
		$user->push();

		$comment = Comment::factory()->create(['create_user_id' => $user->id]);

		Carbon::setTestNow(now()->addSeconds(604800)->subSeconds(100));

		$this->assertTrue($user->can('update', $comment));

		Carbon::setTestNow(now()->addSeconds(604800)->addSeconds(100));

		$this->assertFalse($user->can('update', $comment));
	}
}
