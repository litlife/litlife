<?php

namespace Tests\Feature\Collection\Comment;

use App\Collection;
use App\Comment;
use App\User;
use Tests\TestCase;

class CollectionCommentEditPolicyTest extends TestCase
{
	public function testCanIfHasPermission()
	{
		$user = User::factory()->create();
		$user->group->edit_or_delete_your_comments_to_collections = true;
		$user->push();

		$collection = Collection::factory()->create(['create_user_id' => $user->id]);

		$comment = factory(Comment::class)
			->make(['create_user_id' => $user->id]);

		$collection->comments()->save($comment);

		$this->assertTrue($user->can('update', $comment));
	}

	public function testCantIfDoesntHavePermission()
	{
		$user = User::factory()->create();
		$user->group->edit_or_delete_your_comments_to_collections = false;
		$user->push();

		$collection = Collection::factory()->create(['create_user_id' => $user->id]);

		$comment = factory(Comment::class)
			->make(['create_user_id' => $user->id]);

		$collection->comments()->save($comment);

		$this->assertFalse($user->can('update', $comment));
	}
}
