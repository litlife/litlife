<?php

namespace Tests\Feature\Comment;

use App\Comment;
use App\User;
use Tests\TestCase;

class CommentUpdateTest extends TestCase
{
	public function testEditHttp()
	{
		$user = factory(User::class)->states('with_user_group')->create();

		$comment = factory(Comment::class)->create();
		$comment->create_user()->associate($user);
		$comment->save();

		$this->actingAs($user)
			->get(route('comments.edit', $comment))
			->assertForbidden();

		$user->group->comment_edit_my = true;
		$user->push();
		$user->refresh();

		$this->actingAs($user)
			->get(route('comments.edit', $comment))
			->assertOk();
	}

	public function testEditHttpIfBookDeleted()
	{
		$comment = factory(Comment::class)
			->create();

		$user = $comment->create_user;
		$user->group->comment_edit_my = true;
		$user->push();
		$user->refresh();

		$book = $comment->commentable;

		$book->delete();
		$comment->refresh();
		$book->refresh();

		$this->assertTrue($book->trashed());

		$this->actingAs($user)
			->get(route('comments.edit', $comment))
			->assertOk();
	}

	public function testUpdateHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$comment = factory(Comment::class)
			->create();

		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->patch(route('comments.update', ['comment' => $comment->id]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$comment->refresh();

		$this->assertEquals($comment->text, $text);
		$this->assertEquals($comment->getCharacterCountInText($text), $comment->characters_count);
	}

	public function testUpdatePrivateCommentHttpAndDontAutoPublishFix()
	{
		$comment = factory(Comment::class)
			->states('private')
			->create();

		$user = $comment->create_user;

		$text = $this->faker->realText(100);

		$this->assertTrue($comment->isPrivate());

		$this->actingAs($user)
			->patch(route('comments.update', ['comment' => $comment->id]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$comment->refresh();

		$this->assertEquals($comment->text, $text);
		$this->assertTrue($comment->isPrivate());
	}
}
