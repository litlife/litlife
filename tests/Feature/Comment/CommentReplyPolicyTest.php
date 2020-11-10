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
		$relation = UserRelation::factory()->create([
				'status' => UserRelationType::Blacklist
			]);

		$commentCreator = $relation->first_user;
		$dislikerUser = $relation->second_user;
		$dislikerUser->push();

		$this->assertTrue($commentCreator->hasAddedToBlacklist($dislikerUser));

		$comment = Comment::factory()->book()->create();

		$this->assertFalse($dislikerUser->can('reply', $comment));
	}

	public function testFalseIfCommentOnReview()
	{
		$user = User::factory()->admin()->create();

		$comment = Comment::factory()->sent_for_review()->create();

		$this->assertFalse($user->can('reply', $comment));
	}
}
