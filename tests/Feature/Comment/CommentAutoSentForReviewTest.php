<?php

namespace Tests\Feature\Comment;

use App\Book;
use App\Comment;
use App\User;
use App\Variable;
use Illuminate\Support\Str;
use Tests\TestCase;

class CommentAutoSentForReviewTest extends TestCase
{
	public function testSentForReviewIfFoundCheckWordInTextIfString()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create();

		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$checkWord = Str::random(8) . ' ' . mb_strtolower(Str::random(8));

		Variable::updateOrCreate(
			['name' => 'settings'],
			['value' => ['check_words_in_comments' => $checkWord]]
		);
		/*
				$settings = Variable::where('name', 'settings')->first();
				dd($settings->value);
		*/
		$text = $this->faker->realText(100) . ' ' . mb_strtoupper($checkWord) . ' ' . $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$comment = $book->comments()->first();

		$this->assertTrue($comment->isSentForReview());
	}

	public function testSentForReviewIfFoundCheckWordInText()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create();

		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$checkWord = Str::random(8) . ' ' . mb_strtolower(Str::random(8));

		Variable::updateOrCreate(
			['name' => 'settings'],
			['value' => ['check_words_in_comments' => [$checkWord]]]
		);
		/*
				$settings = Variable::where('name', 'settings')->first();
				dd($settings->value);
		*/
		$text = $this->faker->realText(100) . ' ' . mb_strtoupper($checkWord) . ' ' . $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$comment = $book->comments()->first();

		$this->assertTrue($comment->isSentForReview());
	}

	public function testStoreOkIfNoSettingExists()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create();

		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		Variable::where('name', 'settings')->delete();

		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$comment = $book->comments()->first();

		$this->assertNotNull($comment);
	}

	public function testSentForReviewIfFoundEmail()
	{
		$book = factory(Book::class)
			->states('accepted')
			->create();

		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$text = $this->faker->realText(100) . ' ' . $this->faker->email;

		$this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$comment = $book->comments()->first();

		$this->assertTrue($comment->isSentForReview());
	}

	public function testAcceptedIfAfterUpdateNoFoundEmail()
	{
		$comment = factory(Comment::class)
			->states('sent_for_review')
			->create(['bb_text' => $this->faker->realText(100) . ' ' . $this->faker->email]);

		$user = $comment->create_user;
		$user->group->comment_self_edit_only_time = true;
		$user->push();
		$user->refresh();

		$this->assertTrue($comment->isSentForReview());

		$this->actingAs($user)
			->patch(route('comments.update', ['comment' => $comment]),
				['bb_text' => $this->faker->realText(100)])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$comment->refresh();

		$this->assertTrue($comment->isAccepted());
	}
}


