<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\User;
use Tests\TestCase;

class CommentComplainTest extends TestCase
{
	public function testCanComplain()
	{
		$user = User::factory()->create();
		$user->group->complain = true;
		$user->push();
		$user->refresh();

		$comment = Comment::factory()->create();

		$this->assertTrue($user->can('complain', $comment));
	}
}
