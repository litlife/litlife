<?php

namespace Tests\Feature\Comment;

use App\Author;
use App\Book;
use App\BookStatus;
use App\BookVote;
use App\Comment;
use App\Genre;
use App\Jobs\User\UpdateUserCommentsCount;
use App\Notifications\NewCommentReplyNotification;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\TestCase;

class CommentTest extends TestCase
{
	public function testStoreHttp()
	{
		Notification::fake();

		$book = factory(Book::class)
			->states('with_writer', 'with_compiler', 'with_translator', 'with_illustrator', 'with_editor')
			->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
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

	public function testReply()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$parent = factory(Comment::class)
			->create([
				'commentable_type' => 'book',
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
	}

	public function testCreateIfBookPrivatePolicy()
	{
		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$user = factory(User::class)->create();

		$book = factory(Book::class)->create(['create_user_id' => $user->id]);
		$book->statusPrivate();
		$book->push();
		$book->refresh();

		$this->assertTrue($user->can('commentOn', $book));

		$user->group->add_comment = true;
		$user->push();

		$this->assertTrue($user->can('commentOn', $book));

		$book = factory(Book::class)->create();
		$book->statusPrivate();
		$book->push();
		$book->refresh();

		$this->assertTrue($user->can('commentOn', $book));
	}

	public function testCreateIfCommentsClosedPolicy()
	{
		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$book = factory(Book::class)
			->create(['create_user_id' => $user->id]);

		$this->assertTrue($user->can('commentOn', $book));

		$book->comments_closed = true;
		$book->push();

		$this->assertFalse($user->can('commentOn', $book));
	}

	public function testFulltextSearch()
	{
		$author = Comment::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	public function testCommentsOnCheck()
	{
		$user = factory(User::class)->create();
		$user->group->check_post_comments = true;
		$user->push();

		$comment = factory(Comment::class)->create();
		$comment->statusSentForReview();
		$comment->save();
		$comment->refresh();

		$this->assertTrue($comment->isSentForReview());

		$this->actingAs($user)
			->get(route('comments.on_check'))
			->assertOk()
			->assertSeeText($comment->text);
	}

	public function testApprove()
	{
		$user = factory(User::class)->create();
		$user->group->check_post_comments = true;
		$user->push();

		foreach (Comment::sentOnReview()->get() as $comment) {
			$comment->forceDelete();
		}

		$this->assertEquals(0, Comment::getCachedOnModerationCount());

		$comment = factory(Comment::class)->create();
		$comment->statusSentForReview();
		$comment->save();

		Comment::flushCachedOnModerationCount();
		$this->assertEquals(1, Comment::getCachedOnModerationCount());

		$this->actingAs($user)
			->get(route('comments.approve', compact('comment')))
			->assertOk();

		$this->assertTrue($comment->fresh()->isAccepted());

		$this->assertEquals(0, Comment::getCachedOnModerationCount());
	}

	public function testDecline()
	{
		$user = factory(User::class)->create();
		$user->group->delete_my_comment = true;
		$user->group->delete_other_user_comment = true;
		$user->push();

		foreach (Comment::sentOnReview()->get() as $comment) {
			$comment->forceDelete();
		}

		$this->assertEquals(0, Comment::getCachedOnModerationCount());

		$comment = factory(Comment::class)->create();
		$comment->statusSentForReview();
		$comment->save();

		Comment::flushCachedOnModerationCount();
		$this->assertEquals(1, Comment::getCachedOnModerationCount());

		$this->actingAs($user)
			->delete(route('comments.destroy', ['comment' => $comment]))
			->assertOk();

		$this->assertTrue($comment->fresh()->trashed());

		$this->assertEquals(0, Comment::getCachedOnModerationCount());
	}

	public function testUserReadedBooksHttp()
	{
		$user = factory(User::class)
			->create();

		$comment = factory(Comment::class)
			->create();

		$this->actingAs($user)
			->get(route('users.books.readed.comments', $user))
			->assertDontSee($comment->text);

		$book_status = factory(BookStatus::class)
			->create([
				'book_id' => $comment->commentable->id,
				'user_id' => $user->id,
				'status' => 'read_now'
			]);

		$this->actingAs($user)
			->get(route('users.books.readed.comments', $user))
			->assertDontSee($comment->text);

		$book_status->status = 'readed';
		$book_status->save();

		$this->actingAs($user)
			->get(route('users.books.readed.comments', $user))
			->assertSee($comment->text);
	}

	public function testHideFromHomePageIfBookGenreInList()
	{
		$admin = factory(User::class)->create();
		$admin->group->admin_panel_access = true;
		$admin->push();

		$user = factory(User::class)->create();
		$user->group->add_comment = true;
		$user->push();

		$slash = factory(Genre::class)->create();
		$genre = factory(Genre::class)->create();

		$slash_book = factory(Book::class)->create();
		$slash_book->genres()->sync([$slash->id]);

		$book = factory(Book::class)->create();
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

	public function testBookWithoutAnyGenre()
	{
		$book = factory(Book::class)->create();
		$book->genres()->detach();

		$this->assertEquals(0, $book->genres()->count());

		$user = factory(User::class)->create();
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

	public function testViewLatestIfOnReview()
	{
		$comment = factory(Comment::class)->create();
		$comment->statusSentForReview();
		$comment->save();

		$user = factory(User::class)
			->create();

		$this->actingAs($comment->create_user)
			->get(route('home.latest_comments'))
			->assertOk()
			->assertSeeText($comment->text);

		$this->actingAs($user)
			->get(route('home.latest_comments'))
			->assertOk()
			->assertDontSeeText($comment->text);
	}

	public function testViewInTopicIfOnReview()
	{
		$comment = factory(Comment::class)->create();
		$comment->statusSentForReview();
		$comment->save();

		$user = factory(User::class)
			->create();

		$this->actingAs($comment->create_user)
			->get(route('books.show', $comment->book->id))
			->assertOk()
			->assertSeeText($comment->text);

		$this->actingAs($user)
			->get(route('books.show', $comment->book->id))
			->assertOk()
			->assertDontSeeText($comment->text)
			->assertSeeText(trans_choice('comment.on_check', 1));
	}

	public function testViewInUserCommentList()
	{
		$comment = factory(Comment::class)->create();
		$comment->statusSentForReview();
		$comment->save();

		UpdateUserCommentsCount::dispatch($comment->create_user);

		$this->assertEquals(1, $comment->create_user->comment_count);

		$user = factory(User::class)
			->create();

		$this->actingAs($comment->create_user)
			->get(route('users.books.comments', ['user' => $comment->create_user->id]))
			->assertOk()
			->assertSeeText($comment->text);

		$this->actingAs($user)
			->get(route('users.books.comments', ['user' => $comment->create_user->id]))
			->assertOk()
			->assertDontSeeText($comment->text)
			->assertSeeText(trans_choice('comment.on_check', 1));
	}


	public function testCreateIfBookDoesntHaveAuthor()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$book->authors()->detach();

		$user = factory(User::class)->create();
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

	public function testNotificationEmailSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$notifiable = factory(User::class)->states('with_confirmed_email')->create();
		$notifiable->email_notification_setting->comment_reply = true;
		$notifiable->email_notification_setting->db_comment_reply = false;
		$notifiable->push();

		$parent = factory(Comment::class)
			->create(['create_user_id' => $notifiable->id]);

		$comment = factory(Comment::class)
			->create(['parent' => $parent->id]);

		Notification::assertSentTo(
			$notifiable,
			NewCommentReplyNotification::class,
			function ($notification, $channels) use ($comment) {
				$this->assertContains('mail', $channels);
				$this->assertNotContains('database', $channels);

				return $notification->comment->id == $comment->id;
			}
		);

		Notification::assertSentTo(
			[$notifiable], NewCommentReplyNotification::class
		);
	}

	public function testNotificationDatabaseSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$notifiable = factory(User::class)->create();
		$notifiable->email_notification_setting->comment_reply = false;
		$notifiable->email_notification_setting->db_comment_reply = true;
		$notifiable->push();

		$parent = factory(Comment::class)
			->create(['create_user_id' => $notifiable->id]);

		$comment = factory(Comment::class)
			->create(['parent' => $parent->id]);

		Notification::assertSentTo(
			$notifiable,
			NewCommentReplyNotification::class,
			function ($notification, $channels) use ($comment, $notifiable) {
				$this->assertNotContains('mail', $channels);
				$this->assertContains('database', $channels);

				$data = $notification->toArray($notifiable);

				$this->assertEquals(__('notification.comment_reply.subject'), $data['title']);
				$this->assertEquals(__('notification.comment_reply.line', ['userName' => $comment->create_user->userName]), $data['description']);
				$this->assertEquals(route('comments.go', ['comment' => $comment]), $data['url']);

				return $notification->comment->id == $comment->id;
			}
		);

		Notification::assertSentTo(
			[$notifiable], NewCommentReplyNotification::class
		);
	}

	public function testNotificationSended()
	{
		Notification::fake();
		Notification::assertNothingSent();

		$create_user = factory(User::class)->create();

		$create_user->email_notification_setting->comment_reply = true;
		$create_user->email_notification_setting->save();

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $create_user->id
			]);

		$reply = factory(Comment::class)->make();
		$reply->parent = $comment;
		$reply->save();

		Notification::assertSentTo([$create_user], NewCommentReplyNotification::class);
	}

	public function testCommentToFast()
	{
		$user = factory(User::class)
			->create();
		$user->group->add_comment = true;
		$user->push();

		$book = factory(Book::class)
			->create();

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

	public function testCreateUserAuthorOfBook()
	{
		$author = factory(Author::class)
			->states(['with_author_manager', 'with_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $user->id,
				'commentable_id' => $book->id
			]);

		$this->assertTrue($comment->isCreateUserAuthorOfBook());
	}

	public function testCreateUserNotAuthorOfBook()
	{
		$author = factory(Author::class)
			->states(['with_author_manager', 'with_book'])
			->create()->fresh();

		$book = $author->books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'commentable_id' => $book->id
			]);

		$this->assertFalse($comment->isCreateUserAuthorOfBook());
	}

	public function testCreateUserAuthorOnReviewOfBook()
	{
		$author = factory(Author::class)
			->states(['with_author_manager_on_review', 'with_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $user->id,
				'commentable_id' => $book->id
			]);

		$this->assertFalse($comment->isCreateUserAuthorOfBook());
	}

	public function testCreateUserEditorOfBook()
	{
		$author = factory(Author::class)
			->states(['with_editor_manager', 'with_book'])
			->create()->fresh();

		$manager = $author->managers()->get()->first();
		$this->assertNotNull($manager);

		$user = $manager->user;
		$this->assertNotNull($manager);

		$book = $author->books()->get()->first();
		$this->assertNotNull($book);

		$comment = factory(Comment::class)
			->create([
				'create_user_id' => $user->id,
				'commentable_id' => $book->id
			]);

		$this->assertFalse($comment->isCreateUserAuthorOfBook());
	}

	public function testBBEmpty()
	{
		$comment = factory(Comment::class)
			->create();

		$this->expectException(QueryException::class);

		$comment->bb_text = '';
		$comment->save();
	}

	public function testCollectionComment()
	{
		$comment = factory(Comment::class)
			->states('collection')
			->create(['commentable_type' => 18]);

		$this->assertEquals(18, $comment->commentable_type);
		$this->assertEquals('Collection', $comment->getCommentableModelName());
		$this->assertTrue($comment->isCollectionType());
	}

	public function testBookComment()
	{
		$comment = factory(Comment::class)
			->create(['commentable_type' => 'book']);

		$this->assertEquals('book', $comment->commentable_type);
		$this->assertEquals('Book', $comment->getCommentableModelName());
		$this->assertTrue($comment->isBookType());
	}

	public function testRelationUserBookVote()
	{
		$user = factory(User::class)->create();

		$comment = factory(Comment::class)
			->create([
				'commentable_type' => 'book',
				'create_user_id' => $user->id
			]);

		$book = $comment->commentable;

		$this->assertInstanceOf(Book::class, $book);

		$vote = factory(BookVote::class)
			->create([
				'book_id' => $book->id,
				'create_user_id' => $user->id
			])->fresh();

		$vote2 = factory(BookVote::class)
			->create([
				'book_id' => $book->id
			])->fresh();

		$vote3 = factory(BookVote::class)
			->create([
				'create_user_id' => $user->id
			])->fresh();

		$comment->refresh();

		$comments = Comment::where('id', $comment->id)
			->with('userBookVote')
			->get();

		$vote4 = $comments->first()->userBookVote;

		$this->assertEquals($vote, $vote4);
	}

	public function testIsOkIfBookDeleted()
	{
		$comment = factory(Comment::class)
			->create();

		$this->assertTrue($comment->isBookType());

		$book = $comment->commentable;

		$this->get(route('books.show', $book))
			->assertOk();

		$comment->commentable->delete();

		$this->get(route('books.show', $book))
			->assertNotFound();
	}

	public function testIsOkDeleteIfBookSoftDeleted()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$comment = factory(Comment::class)
			->create();

		$book = $comment->commentable;
		$book->delete();

		$this->actingAs($user)
			->delete(route('comments.destroy', $comment))
			->assertOk();

		$comment->refresh();

		$this->assertSoftDeleted($comment);
	}

	public function testIsOkDeleteIfBookForceDeleted()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$comment = factory(Comment::class)
			->create();

		$book = $comment->commentable;
		$book->forceDelete();

		$this->actingAs($user)
			->delete(route('comments.destroy', $comment))
			->assertOk();

		$comment->refresh();

		$this->assertSoftDeleted($comment);
	}

	public function testUpperCaseLettersCount()
	{
		$text = '[I]текст[/I][b]Текст Текст Текст[/b]';

		$comment = factory(Comment::class)
			->create(['bb_text' => $text]);

		$this->assertEquals(3, $comment->getUpperCaseCharactersCount($comment->getContent()));
		$this->assertEquals(15, $comment->getUpperCaseLettersPercent($comment->getContent()));

		$text = ' ТЕКСТ текст';

		$comment = factory(Comment::class)
			->create(['bb_text' => $text]);

		$this->assertEquals(5, $comment->getUpperCaseCharactersCount($comment->getContent()));
		$this->assertEquals(50, $comment->getUpperCaseLettersPercent($comment->getContent()));

		$text = ' ТЕКСТ ТЕКСТ тек';

		$comment = factory(Comment::class)
			->create(['bb_text' => $text]);

		$this->assertEquals(10, $comment->getUpperCaseCharactersCount($comment->getContent()));
		$this->assertEquals(77, $comment->getUpperCaseLettersPercent($comment->getContent()));

		$text = ' :) ';

		$comment = factory(Comment::class)
			->create(['bb_text' => $text]);

		$this->assertEquals(0, $comment->getUpperCaseCharactersCount($comment->getContent()));
		$this->assertEquals(0, $comment->getUpperCaseLettersPercent($comment->getContent()));
	}

	public function testSentForReviewIfExternalLinksAndLackOfCommentsCount()
	{
		$text = 'текст [url]http://example.com/test[/url] текст [url]http://example.com/test[/url]';

		$comment = factory(Comment::class)
			->create(['bb_text' => $text]);

		$this->assertTrue($comment->isSentForReview());
	}

	public function testAcceptedIfExternalLinksAndEnoughOfCommentsCount()
	{
		$user = factory(User::class)->create();
		$user->comment_count = 100;
		$user->save();

		$text = 'текст [url]http://example.com/test[/url] текст [url]http://example.com/test[/url]';

		$comment = factory(Comment::class)
			->create(['create_user_id' => $user->id, 'bb_text' => $text]);

		$this->assertTrue($comment->isAccepted());
	}

	public function testAcceptedIfExternalLinksAndEnoughOfPostsCount()
	{
		$user = factory(User::class)->create();
		$user->forum_message_count = 100;
		$user->save();

		$text = 'текст [url]http://example.com/test[/url] текст [url]http://example.com/test[/url]';

		$comment = factory(Comment::class)
			->create(['create_user_id' => $user->id, 'bb_text' => $text]);

		$this->assertTrue($comment->isAccepted());
	}

	public function testSentForReviewIfTextUpperCase()
	{
		$text = 'ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ';

		$comment = factory(Comment::class)
			->create(['bb_text' => $text]);

		$this->assertTrue($comment->isSentForReview());
	}

	public function testAcceptedIfTextUpperCase2()
	{
		$text = 'ТЕ';

		$comment = factory(Comment::class)
			->create(['bb_text' => $text]);

		$this->assertTrue($comment->isAccepted());
	}

	public function testPerPage2()
	{
		$response = $this->get(route('home.latest_comments', ['per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['comments']->perPage());

		$response = $this->get(route('home.latest_comments', ['per_page' => 200]))
			->assertOk();

		$this->assertEquals(100, $response->original->gatherData()['comments']->perPage());
	}

	public function testPerPage()
	{
		$author = factory(Author::class)->create();

		$response = $this->get(route('authors.comments', ['author' => $author, 'per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['comments']->perPage());

		$response = $this->get(route('authors.comments', ['author' => $author, 'per_page' => 200]))
			->assertOk();

		$this->assertEquals(100, $response->original->gatherData()['comments']->perPage());
	}

	public function testCreateHttpIfRelationNotFound()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$this->actingAs($user)
			->get(route('comments.create', [
				'commentable_type' => Str::random(10),
				'commentable_id' => rand(1, 1000)
			]))
			->assertNotFound();
	}

	public function testStoreHttpIfRelationNotFound()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$this->actingAs($user)
			->post(route('comments.store', [
				'commentable_type' => Str::random(10),
				'commentable_id' => rand(1, 1000),
			]), ['bb_text' => Str::random(10)])
			->assertNotFound();
	}

	public function testSetGetParentComment()
	{
		$comment = factory(Comment::class)
			->create();

		$comment2 = new Comment();
		$comment2->parent = $comment;

		$this->assertTrue($comment2->parent->is($comment));
	}

	public function testCantReplyIfCommentOnReview()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$comment = factory(Comment::class)
			->states('sent_for_review')
			->create();

		$this->assertFalse($user->can('reply', $comment));
	}

	public function testOriginCommentableRelation()
	{
		$comment = factory(Comment::class)
			->states('book')
			->create();

		$book = $comment->commentable;

		$this->assertEquals($book->origin_commentable_id, $book->commentable_id);
		$this->assertInstanceOf(Book::class, $comment->commentable);
		$this->assertInstanceOf(Book::class, $comment->originCommentable);
		$this->assertEquals($comment->commentable->id, $comment->originCommentable->id);

		$comment->load(['commentable', 'originCommentable']);

		$this->assertEquals($book->origin_commentable_id, $book->commentable_id);
		$this->assertInstanceOf(Book::class, $comment->commentable);
		$this->assertInstanceOf(Book::class, $comment->originCommentable);
		$this->assertEquals($comment->commentable->id, $comment->originCommentable->id);
	}

	public function testDeleteCommentIfCreatorDeleted()
	{
		$comment = factory(Comment::class)
			->states('book')
			->create();

		$comment->create_user->delete();
		$comment->refresh();
		$comment->delete();

		$this->assertTrue($comment->trashed());

		$comment = factory(Comment::class)
			->states('book')
			->create();

		$comment->create_user->forceDelete();
		$comment->refresh();
		$comment->delete();

		$this->assertTrue($comment->trashed());
	}
	/*
		public function testForbidBBCodeTags()
		{
			$text = '[b][size]текст[/size][/b]';

			$comment = new Comment;
			$comment->setForbidTags(['size']);
			$comment->setBBCode($text);

			$this->assertEquals('[b]текст[/b]', $comment->getBBCode());

			$text = '[size=5][b]текст[/b][/size]';

			$comment = new Comment;
			$comment->setForbidTags(['size']);
			$comment->setBBCode($text);

			$this->assertEquals('[b]текст[/b]', $comment->getBBCode());

			$text = '[code][size=5][b]текст[/b][/size][/code]';

			$comment = new Comment;
			$comment->setForbidTags(['size']);
			$comment->setBBCode($text);

			$this->assertEquals('[code][size=5][b]текст[/b][/size][/code]', $comment->getBBCode());

			$text = '[code][size=5][b][code][size=5]текст[/size][/code][/b][/size][/code]';

			$comment = new Comment;
			$comment->setForbidTags(['size']);
			$comment->setBBCode($text);

			$this->assertEquals('[code][size=5][b][code][size=5]текст[/size][/code][/b][/size][/code]',
				$comment->getBBCode());

			$text = '[code][size=5][b]text[/b][/size][/code]text[code][size=5]текст[/size][/code]';

			$comment = new Comment;
			$comment->setForbidTags(['size']);
			$comment->setBBCode($text);

			$this->assertEquals('[code][size=5][b]text[/b][/size][/code]text[code][size=5]текст[/size][/code]',
				$comment->getBBCode());
		}
	*/
}
