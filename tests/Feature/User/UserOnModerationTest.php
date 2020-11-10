<?php

namespace Tests\Feature\User;

use App\Book;
use App\Topic;
use App\User;
use App\UserOnModeration;
use Tests\TestCase;

class UserOnModerationTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testCreateCommentHttp()
	{
		$user_on_moderation = UserOnModeration::factory()->create();

		$book = Book::factory()->create();

		$user = $user_on_moderation->user;
		$user->group->add_comment = true;
		$user->push();

		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => 'book',
				'commentable_id' => $book]),
				['bb_text' => $text])
			->assertRedirect();

		$comment = $user->comments()->first();

		$this->assertTrue($comment->isSentForReview());

		$this->actingAs($user)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertSeeText($text);

		$admin = User::factory()->create();
		$admin->group->check_post_comments = true;
		$admin->push();

		$this->actingAs($admin)
			->get(route('books.show', ['book' => $book]))
			->assertOk()
			->assertDontSeeText($text);

		$this->actingAs($admin)
			->get(route('comments.on_check'))
			->assertOk()
			->assertSeeText($text);
	}

	public function testCreatePostHttp()
	{
		$user_on_moderation = UserOnModeration::factory()->create();

		$topic = Topic::factory()->create();

		$user = $user_on_moderation->user;
		$user->group->add_comment = true;
		$user->push();

		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('posts.store', ['topic' => $topic->id]),
				['bb_text' => $text])
			->assertRedirect();

		$post = $user->posts()->first();

		$this->assertTrue($post->isSentForReview());

		$this->actingAs($user)
			->get(route('topics.show', ['topic' => $topic]))
			->assertOk()
			->assertSeeText($text);

		$admin = User::factory()->create();
		$admin->group->check_post_comments = true;
		$admin->push();

		$this->actingAs($admin)
			->get(route('topics.show', ['topic' => $topic]))
			->assertOk()
			->assertDontSeeText($text);

		$this->actingAs($admin)
			->get(route('posts.on_check'))
			->assertOk()
			->assertSeeText($text);
	}

	public function testIsOnModeration()
	{
		$user_on_moderation = UserOnModeration::factory()->create();

		$user = $user_on_moderation->user;

		$this->assertTrue($user->isOnModeration());

		$user_on_moderation->delete();
		$user->refresh();

		$this->assertFalse($user->isOnModeration());
	}

	public function testAddUserOnModeration()
	{
		$admin = User::factory()->admin()->create();

		$user = User::factory()->create();

		$this->assertFalse($user->isOnModeration());

		$this->actingAs($admin)
			->get(route('users.moderations.add', ['user' => $user]))
			->assertRedirect();

		$user->refresh();

		$this->assertTrue($user->isOnModeration());
	}

	public function testRemoveUserOnModeration()
	{
		$admin = User::factory()->admin()->create();

		$user_on_moderation = UserOnModeration::factory()->create();

		$user = $user_on_moderation->user;

		$this->assertTrue($user->isOnModeration());

		$this->actingAs($admin)
			->get(route('users.moderations.remove', ['user' => $user]))
			->assertRedirect();

		$user->refresh();

		$this->assertFalse($user->isOnModeration());
	}
}
