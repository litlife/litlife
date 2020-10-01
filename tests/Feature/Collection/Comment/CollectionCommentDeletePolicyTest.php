<?php

namespace Tests\Feature\Collection\Book;

use App\Collection;
use App\Comment;
use App\User;
use Tests\TestCase;

class CollectionCommentDeletePolicyTest extends TestCase
{
	public function testCanDeleteIfUserCreatorOfCollection()
	{
		$user = factory(User::class)->create();

		$collection = factory(Collection::class)
			->create(['create_user_id' => $user->id]);

		$comment = factory(Comment::class)
			->make(['create_user_id' => $user->id]);

		$collection->comments()->save($comment);

		$this->assertTrue($user->can('delete', $comment));
	}
}
