<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\Enums\UserRelationType;
use App\UserRelation;
use Tests\TestCase;

class CommentVotePolicyTest extends TestCase
{
	public function testDisableVoteIfUserInBlacklist()
	{
		$relation = factory(UserRelation::class)
			->create([
				'status' => UserRelationType::Blacklist
			]);

		$commentCreator = $relation->first_user;
		$dislikerUser = $relation->second_user;
		$dislikerUser->group->comment_add_vote = true;
		$dislikerUser->push();

		$this->assertTrue($commentCreator->hasAddedToBlacklist($dislikerUser));

		$comment = factory(Comment::class)
			->states('book')
			->create(['create_user_id' => $commentCreator->id]);

		$this->assertFalse($dislikerUser->can('vote', $comment));
	}
}
