<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\Enums\UserRelationType;
use App\User;
use App\UserRelation;
use Tests\TestCase;

class CommentReplyPolicyTest extends TestCase
{
	public function testFalseIfIfCreatorAddedToBlacklist()
	{
		$relation = factory(UserRelation::class)
			->create([
				'status' => UserRelationType::Blacklist
			]);

		$commentCreator = $relation->first_user;
		$dislikerUser = $relation->second_user;
		$dislikerUser->push();

		$this->assertTrue($commentCreator->hasAddedToBlacklist($dislikerUser));

		$comment = factory(Comment::class)
			->states('book')
			->create(['create_user_id' => $commentCreator->id]);

		$this->assertFalse($dislikerUser->can('reply', $comment));
	}

	public function testFalseIfCommentOnReview()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$comment = factory(Comment::class)
			->states('sent_for_review')
			->create();

		$this->assertFalse($user->can('reply', $comment));
	}
}
