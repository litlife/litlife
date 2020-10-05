<?php

namespace Tests\Feature\Collection\Comment;

use App\Collection;
use App\CollectionUser;
use App\Comment;
use App\User;
use Tests\TestCase;

class CollectionCommentViewPolicyTest extends TestCase
{
	public function testCanIfCollectionAccepted()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$comment = factory(Comment::class)
			->make();

		$collection->comments()->save($comment);

		$comment->refresh();

		$user = factory(User::class)->create();

		$this->assertTrue($user->can('view', $comment));
	}

	public function testCanIfCollectionPrivateAndUserIsCollectionCreator()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$user = $collection->create_user;

		$comment = factory(Comment::class)
			->make();

		$collection->comments()->save($comment);

		$comment->refresh();

		$this->assertTrue($user->can('view', $comment));
	}

	public function testCanIfCollectionPrivateAndUserIsParticipant()
	{
		$collectionUser = factory(CollectionUser::class)
			->create();

		$collection = $collectionUser->collection;
		$collection->statusPrivate();
		$collection->save();

		$user = $collectionUser->user;

		$comment = factory(Comment::class)
			->make();

		$collection->comments()->save($comment);

		$comment->refresh();

		$this->assertTrue($user->can('view', $comment));
	}

	public function testCantIfCollectionPrivateAndUserIsNotParticipant()
	{
		$collectionUser = factory(CollectionUser::class)
			->create();

		$collection = $collectionUser->collection;
		$collection->statusPrivate();
		$collection->save();

		$comment = factory(Comment::class)
			->make();

		$collection->comments()->save($comment);

		$comment->refresh();

		$user = factory(User::class)->create();

		$this->assertFalse($user->can('view', $comment));
	}
}
