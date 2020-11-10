<?php

namespace Tests\Feature\Comment;

use App\Book;
use App\Comment;
use App\Genre;
use App\Notifications\NewCommentReplyNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class CommentCreateTest extends TestCase
{
	public function testStoreHttp()
	{
		Notification::fake();

		$book = Book::factory()->with_writer()->with_compiler()->with_translator()->with_illustrator()->with_editor()->create();
		$book->statusAccepted();
		$book->save();

		$user = User::factory()->create();
		$user->group->add_comment = true;
		$user->push();

		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$comment = $book->comments()->first();

		$this->assertEquals($comment->text, $text);
		$this->assertEquals($comment->getCharacterCountInText($text), $comment->characters_count);
		$this->assertTrue($comment->isAccepted());

		$this->assertEquals(1, $book->comment_count);
		$this->assertEquals(1, $book->translators()->first()->comments_count);
		$this->assertEquals(1, $book->writers()->first()->comments_count);
		$this->assertEquals(1, $book->compilers()->first()->comments_count);
		$this->assertEquals(1, $book->editors()->first()->comments_count);
		$this->assertEquals(1, $book->illustrators()->first()->comments_count);
		$this->assertEquals($comment->origin_commentable_id, $comment->commentable_id);

		Notification::assertNothingSent();
	}

	public function testCreateHttpIfRelationNotFound()
	{
		$user = User::factory()->admin()->create();

		$this->actingAs($user)
			->get(route('comments.create', [
				'commentable_type' => Str::random(10),
				'commentable_id' => rand(1, 1000)
			]))
			->assertNotFound();
	}

	public function testStoreHttpIfRelationNotFound()
	{
		$user = User::factory()->admin()->create();

		$this->actingAs($user)
			->post(route('comments.store', [
				'commentable_type' => Str::random(10),
				'commentable_id' => rand(1, 1000),
			]), ['bb_text' => Str::random(10)])
			->assertNotFound();
	}

	public function testCommentToFast()
	{
		$user = User::factory()->create();
		$user->group->add_comment = true;
		$user->push();

		$book = Book::factory()->create();

		$comments = factory(Comment::class, 10)
			->create([
				'create_user_id' => $user->id,
				'commentable_type' => 'book'
			]);

		$response = $this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $this->faker->text])
			->assertRedirect();

		$response->assertSessionHasErrors(['bb_text' => __('comment.you_comment_to_fast')]);

		Carbon::setTestNow(now()->addMinutes(11));

		$this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $this->faker->text])
			->assertRedirect()
			->assertSessionHasNoErrors();
	}

	public function testCreateIfBookDoesntHaveAuthor()
	{
		$book = Book::factory()->create();
		$book->statusAccepted();
		$book->save();

		$book->authors()->detach();

		$user = User::factory()->create();
		$user->group->add_comment = true;
		$user->push();

		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('comments.store', ['commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$this->assertEquals(1, $book->comment_count);
	}

	public function testBookWithoutAnyGenre()
	{
		$book = Book::factory()->create();
		$book->genres()->detach();

		$this->assertEquals(0, $book->genres()->count());

		$user = User::factory()->create();
		$user->group->add_comment = true;
		$user->push();

		$comment_text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('comments.store',
				['commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $comment_text])
			->assertRedirect();

		$this->get(route('books.show', $book))
			->assertOk()
			->assertSeeText($comment_text);

		$this->get(route('home.latest_comments'))
			->assertOk()
			->assertSeeText($comment_text);
	}

	public function testHideFromHomePageIfBookGenreInList()
	{
		$admin = User::factory()->create();
		$admin->group->admin_panel_access = true;
		$admin->push();

		$user = User::factory()->create();
		$user->group->add_comment = true;
		$user->push();

		$slash = Genre::factory()->create();
		$genre = Genre::factory()->create();

		$slash_book = Book::factory()->create();
		$slash_book->genres()->sync([$slash->id]);

		$book = Book::factory()->create();
		$book->genres()->sync([$genre->id]);

		$this->actingAs($admin)
			->post(route('settings.save'),
				['genres_books_comments_hide_from_home_page' => [$slash->id]])
			->assertRedirect();

		$slash_comment_text = $this->faker->realText(100);
		$comment_text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('comments.store',
				['commentable_type' => 'book', 'commentable_id' => $slash_book->id]),
				['bb_text' => $slash_comment_text])
			->assertRedirect();

		$slash_book_comment = $slash_book->comments()->first();

		$this->actingAs($user)
			->post(route('comments.store',
				['commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $comment_text])
			->assertRedirect();

		$book_comment = $book->comments()->first();

		$this->assertEquals(1, $slash_book_comment->hide_from_top);
		$this->assertEquals(0, $book_comment->hide_from_top);

		$this->get(route('home.latest_comments'))
			->assertOk()
			->assertDontSeeText($slash_comment_text)
			->assertSeeText($comment_text);
	}

	public function testReply()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$book = Book::factory()->accepted()->create();

		$user = User::factory()->create();
		$user->group->add_comment = true;
		$user->push();

		$parent = factory(Comment::class)
			->states('book')
			->create([
				'commentable_id' => $book->id
			]);

		$text = $this->faker->realText(100);

		$this->actingAs($user)
			->post(route('comments.store', ['parent' => $parent->id,
				'commentable_type' => 'book', 'commentable_id' => $book->id]),
				['bb_text' => $text])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$book->refresh();

		$child = $book->comments()->orderBy('id', 'desc')->first();

		$this->assertTrue($parent->isRoot());
		$this->assertEquals($parent->id, $child->parent->id);

		Notification::assertSentTo(
			$parent->create_user,
			NewCommentReplyNotification::class,
			function ($notification, $channels) use ($child) {
				return $notification->comment->id == $child->id;
			}
		);

		Notification::assertNotSentTo(
			$child->create_user, NewCommentReplyNotification::class
		);
	}
}
