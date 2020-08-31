<?php

namespace Tests\Feature\Author;

use App\Book;
use App\User;
use App\UserAuthor;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthorFavoriteBooksTest extends TestCase
{
	public function testView()
	{
		$user_author = factory(UserAuthor::class)
			->create()->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();
		$book->writers()->attach([$user_author->author->id]);

		Carbon::setTestNow(now()->addMinute());

		$this->assertEquals(1, $user->fresh()->getFavoriteAuthorBooksBuilder()->count());
		$this->assertEquals(1, $user->fresh()->getNewFavoriteAuthorsBooksCount());

		Carbon::setTestNow(now()->addMinute());

		$user->data->favorite_authors_books_latest_viewed_at = now();
		$user->data->save();
		$user->flushCachedNewFavoriteAuthorsBooksCount();

		Carbon::setTestNow(now()->addMinute());

		$this->assertEquals(0, $user->fresh()->getNewFavoriteAuthorsBooksCount());
	}

	public function testFlushUsersAddedToFavoritesNewBooksCount()
	{
		$user_author = factory(UserAuthor::class)
			->create()
			->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$this->assertEquals(0, $user->getNewFavoriteAuthorsBooksCount());

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();
		$book->refresh();
		$user_author->author->any_books()->sync([$book->id]);

		$user_author->author->flushUsersAddedToFavoritesNewBooksCount();

		Carbon::setTestNow(now()->addMinute());

		$this->assertEquals(1, $user->getNewFavoriteAuthorsBooksCount());
	}

	public function testNoFavoriteAuthors()
	{
		$user = factory(User::class)
			->create()
			->fresh();

		$this->assertEquals(0, $user->getNewFavoriteAuthorsBooksCount());
	}

	public function testNewBookAccepted()
	{
		$user_author = factory(UserAuthor::class)
			->create()
			->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();
		$book->refresh();

		$user_author->author->any_books()->sync([$book->id]);

		$this->assertEquals(1, $user->getNewFavoriteAuthorsBooksCount());
	}

	public function testNewBookSentForReview()
	{
		$user_author = factory(UserAuthor::class)
			->create()
			->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$book = factory(Book::class)->create();
		$book->statusSentForReview();
		$book->save();
		$book->refresh();

		$user_author->author->any_books()->sync([$book->id]);

		$this->assertEquals(0, $user->getNewFavoriteAuthorsBooksCount());
	}

	public function testNewBookPrivate()
	{
		$user_author = factory(UserAuthor::class)
			->create()
			->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$book = factory(Book::class)->create();
		$book->statusPrivate();
		$book->save();
		$book->refresh();

		$user_author->author->any_books()->sync([$book->id]);

		$this->assertEquals(0, $user->getNewFavoriteAuthorsBooksCount());
	}

	public function testBookPublishRefreshCounterHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->check_books = true;
		$admin->group->add_book_without_check = true;
		$admin->group->edit_other_user_book = true;
		$admin->push();

		$user_author = factory(UserAuthor::class)
			->create()
			->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$this->assertEquals(0, $user->getNewFavoriteAuthorsBooksCount());

		$book = factory(Book::class)->create();
		$book->statusSentForReview();
		$book->save();
		$user_author->author->any_books()->sync([$book->id]);

		$response = $this->followingRedirects()
			->actingAs($admin)
			->get(route('books.make_accepted', $book))
			->assertSessionHasNoErrors()
			->assertOk();

		$this->assertEquals(1, $user->fresh()->getNewFavoriteAuthorsBooksCount());
	}

	public function testBookAddToPrivateRefreshCounterHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->check_books = true;
		$admin->push();

		$user_author = factory(UserAuthor::class)
			->create()
			->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();
		$user_author->author->any_books()->sync([$book->id]);

		$this->assertEquals(1, $user->getNewFavoriteAuthorsBooksCount());

		$response = $this->actingAs($admin)
			->post(route('books.add_to_private', $book))
			->assertSessionHasNoErrors();

		$this->assertTrue($book->fresh()->isPrivate());

		$this->assertEquals(0, $user->fresh()->getNewFavoriteAuthorsBooksCount());
	}

	public function testNewBookDeletedHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->delete_other_user_book = true;
		$admin->push();

		$user_author = factory(UserAuthor::class)
			->create()->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();
		$user_author->author->any_books()->sync([$book->id]);

		$this->assertEquals(1, $user->getNewFavoriteAuthorsBooksCount());

		$response = $this->actingAs($admin)
			->get(route('books.delete', $book))
			->assertSessionHasNoErrors();

		$this->assertTrue($book->fresh()->trashed());

		$this->assertEquals(0, $user->getNewFavoriteAuthorsBooksCount());

		$response = $this->actingAs($admin)
			->get(route('books.restore', $book))
			->assertSessionHasNoErrors();

		$this->assertFalse($book->fresh()->trashed());

		$this->assertEquals(1, $user->getNewFavoriteAuthorsBooksCount());
	}

	public function testPublishSelfAddedBookRefreshCounterHttp()
	{
		$admin = factory(User::class)->create();
		$admin->group->check_books = true;
		$admin->group->add_book_without_check = true;
		$admin->group->edit_other_user_book = true;
		$admin->push();

		$user_author = factory(UserAuthor::class)
			->create()
			->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$this->assertEquals(0, $user->getNewFavoriteAuthorsBooksCount());

		$book = factory(Book::class)
			->create(['create_user_id' => $user->id]);
		$book->statusSentForReview();
		$book->save();
		$user_author->author->any_books()->sync([$book->id]);

		$response = $this->followingRedirects()
			->actingAs($admin)
			->get(route('books.make_accepted', $book))
			->assertSessionHasNoErrors()
			->assertOk();

		$this->assertEquals(0, $user->fresh()->getNewFavoriteAuthorsBooksCount());
	}

	public function testEmptyFavoriteAuthorsBooksLatestViewedAt()
	{
		$user = factory(User::class)
			->create();

		$this->assertNotNull($user->data->favorite_authors_books_latest_viewed_at);
		$this->assertEquals(Carbon::create(2019, 05, 05, 0, 0, 0), $user->data->favorite_authors_books_latest_viewed_at);
	}

	public function testViewBooksListHttp()
	{
		$user_author = factory(UserAuthor::class)
			->create()->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();
		$user_author->author->any_books()->sync([$book->id]);

		Carbon::setTestNow(now()->addMinute());

		$this->assertEquals(1, $user->fresh()->getNewFavoriteAuthorsBooksCount());

		Carbon::setTestNow(now()->addMinute());

		$response = $this->actingAs($user)
			->get(route('users.authors.books', $user))
			->assertOk()
			->assertSeeText($book->title);

		$this->assertEquals(1, $response->original->getData()['books']->count());

		Carbon::setTestNow(now()->addMinute());

		$this->assertEquals(0, $user->fresh()->getNewFavoriteAuthorsBooksCount());
	}

	public function testOtherUserViewBooksListHttp()
	{
		$admin = factory(User::class)
			->create();

		$user_author = factory(UserAuthor::class)
			->create()->fresh();

		Carbon::setTestNow(now()->addMinute());

		$user = $user_author->user;

		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();
		$user_author->author->any_books()->sync([$book->id]);

		Carbon::setTestNow(now()->addMinute());

		$this->assertEquals(1, $user->fresh()->getNewFavoriteAuthorsBooksCount());

		Carbon::setTestNow(now()->addMinute());

		$response = $this->actingAs($admin)
			->get(route('users.authors.books', $user))
			->assertOk()
			->assertSeeText($book->title);

		$this->assertEquals(1, $response->original->getData()['books']->count());

		Carbon::setTestNow(now()->addMinute());

		$this->assertEquals(1, $user->fresh()->getNewFavoriteAuthorsBooksCount());
	}

	public function testDontShowTheBookAgain()
	{
		$title = Str::random(10);

		$user = factory(User::class)->create();

		$user_author = factory(UserAuthor::class)
			->create(['user_id' => $user->id]);

		$user_author2 = factory(UserAuthor::class)
			->create(['user_id' => $user->id]);

		$book = factory(Book::class)->create(['title' => $title]);

		$book->writers()->sync([$user_author->author_id]);
		$book->translators()->sync([$user_author2->author_id]);

		$response = $this->actingAs($user)
			->get(route('users.authors.books', $user))
			->assertOk()
			->assertSeeText($book->title);

		$books = $response->original->getData()['books'];

		$this->assertEquals(1, $books->count());
	}
}
