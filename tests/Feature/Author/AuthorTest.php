<?php

namespace Tests\Feature\Author;

use App\Author;
use App\AuthorStatus;
use App\Book;
use App\BookFile;
use App\Enums\AuthorEnum;
use App\Jobs\Author\UpdateAuthorBooksCount;
use App\Manager;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AuthorTest extends TestCase
{
	public function testIndexHttp()
	{
		$this->get(route('authors'))
			->assertOk();
	}

	public function testName()
	{
		$author = new Author;
		$author->name = 'Lastname  Firstname  Middlename Nickname';

		$this->assertEquals('Lastname', $author->last_name);
		$this->assertEquals('Firstname', $author->first_name);
		$this->assertEquals('Middlename', $author->middle_name);
		$this->assertEquals('Nickname', $author->nickname);
		$this->assertEquals('Lastname Firstname Middlename Nickname', $author->name);

		$author = new Author;
		$author->name = 'Nickname';

		$this->assertEquals('', $author->last_name);
		$this->assertEquals('', $author->first_name);
		$this->assertEquals('', $author->middle_name);
		$this->assertEquals('Nickname', $author->nickname);
		$this->assertEquals('Nickname', $author->name);

		$author = new Author;
		$author->name = 'Lastname  Firstname  ';

		$this->assertEquals('Lastname', $author->last_name);
		$this->assertEquals('Firstname', $author->first_name);
		$this->assertEquals('Lastname Firstname', $author->name);

		$author = new Author;
		$author->name = 'Lastname  Firstname  Middlename  ';

		$this->assertEquals('Lastname', $author->last_name);
		$this->assertEquals('Firstname', $author->first_name);
		$this->assertEquals('Middlename', $author->middle_name);
		$this->assertEquals('Lastname Firstname Middlename', $author->name);


		$author = new Author;
		$author->name = 'Last-name Firstname ';

		$this->assertEquals('Last-name', $author->last_name);
		$this->assertEquals('Firstname', $author->first_name);
		$this->assertEquals('', $author->middle_name);
		$this->assertEquals('Last-name Firstname', $author->name);
	}

	public function testType()
	{
		$book = factory(Book::class)->states('with_writer')->create();
		$book->save();

		$author = factory(Author::class)->create();
		$author->save();

		$translator = factory(Author::class)->create();
		$translator->save();

		$editor = factory(Author::class)->create();
		$editor->save();

		$compiler = factory(Author::class)->create();
		$compiler->save();

		$illustrator = factory(Author::class)->create();
		$illustrator->save();

		$book->writers()->syncWithoutDetaching([$author->id]);
		$book->translators()->syncWithoutDetaching([$translator->id]);
		$book->editors()->syncWithoutDetaching([$editor->id]);
		$book->compilers()->syncWithoutDetaching([$compiler->id]);
		$book->illustrators()->syncWithoutDetaching([$illustrator->id]);

		UpdateAuthorBooksCount::dispatch($author);
		UpdateAuthorBooksCount::dispatch($translator);
		UpdateAuthorBooksCount::dispatch($editor);
		UpdateAuthorBooksCount::dispatch($compiler);
		UpdateAuthorBooksCount::dispatch($illustrator);

		$book->refresh();

		$this->assertEquals(2, $book->writers()->count());
		$this->assertNotNull($book->writers()->first()->pivot->created_at);
		$this->assertEquals(1, $book->translators()->count());
		$this->assertEquals(1, $book->editors()->count());
		$this->assertEquals(1, $book->compilers()->count());
		$this->assertEquals(1, $book->illustrators()->count());
		$this->assertNotNull($book->illustrators()->first()->pivot->created_at);

		$this->assertEquals(6, $book->authors()->count());

		$book->editors()->detach();

		$this->assertEquals(0, $book->editors()->count());
		$this->assertEquals(2, $book->writers()->count());

		$this->assertEquals(1, $illustrator->books_count);
		$this->assertEquals(1, $compiler->books_count);
		$this->assertEquals(1, $editor->books_count);
		$this->assertEquals(1, $translator->books_count);
		$this->assertEquals(1, $author->books_count);
	}

	public function testNew()
	{
		$book = factory(Book::class)->states('with_writer')->create();
		$book->save();

		$author = factory(Author::class)->create();
		$author->save();

		$translator = factory(Author::class)->create();
		$translator->save();

		$editor = factory(Author::class)->create();
		$editor->save();

		$book->writers()->syncWithoutDetaching([$author->id]);
		$book->translators()->syncWithoutDetaching([$translator->id]);
		$book->editors()->syncWithoutDetaching([$editor->id]);
		$book->refresh();
		/*
				foreach ($book->authors as $author)
				{
					dump($author->type);
				}
		*/
		$this->assertEquals(4, $book->authors()->get()->count());
		$this->assertEquals(4, $book->authors->count());
		$this->assertEquals(2, $book->getAuthorsWithType(AuthorEnum::Writer)->count());
		$this->assertEquals(1, $book->getAuthorsWithType(AuthorEnum::Translator)->count());
		$this->assertEquals(1, $book->getAuthorsWithType(AuthorEnum::Editor)->count());

		$book->setRelation('writes', $book->getAuthorsWithType(AuthorEnum::Writer));
		$book->setRelation('editors', $book->getAuthorsWithType(AuthorEnum::Editor));
		$book->setRelation('illustrators', $book->getAuthorsWithType(AuthorEnum::Illustrator));
		$book->setRelation('translators', $book->getAuthorsWithType(AuthorEnum::Translator));
		$book->setRelation('compilers', $book->getAuthorsWithType(AuthorEnum::Compiler));

		$this->assertEquals(2, $book->writers->count());
		$this->assertEquals(1, $book->translators->count());
		$this->assertEquals(1, $book->editors->count());
	}

	public function testShowAuthorPageWithoutBooksHttp()
	{
		$author = factory(Author::class)->create();
		$author->save();

		$this->get(route('authors.show', $author))
			->assertOk();
	}

	public function testAuthorEditWithEmptyBiographyHttp()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->create();
		$user->group->author_edit = true;
		$user->push();

		$author = factory(Author::class)->create();
		$author->save();

		$response = $this->actingAs($user)
			->patch(route('authors.update', $author),
				[
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'gender' => 'male'
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$this->assertEquals(1, $author->activities()->count());
		$activity = $author->activities()->first();
		$this->assertEquals('updated', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testStoreHttp()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->create();

		$response = $this->actingAs($user)
			->post(route('authors.store'),
				[
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'gender' => 'male'
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$author = $user->created_authors()->first();

		$this->assertNotNull($author);

		$this->assertEquals(1, $author->activities()->count());
		$activity = $author->activities()->first();
		$this->assertEquals('created', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testAuthorCreateWithBiographyHttp()
	{
		$user = factory(User::class)->create();
		$user->push();

		$biography = $this->faker->realText(200);

		$response = $this->actingAs($user)
			->post(route('authors.store'),
				[
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'gender' => 'male',
					'biography' => $biography
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$author = $user->created_authors()->first();

		$this->assertNotNull($author);

		$response = $this->get(route('authors.show', $author))
			->assertSeeText($biography);
	}

	public function testFulltextSearch()
	{
		$author = Author::FulltextSearch('Время&—&детство!')->get();

		$this->assertTrue(true);
	}

	public function testMerge()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->create();
		$user->group->merge_authors = true;
		$user->push();

		$main_author = factory(Author::class)
			->states('with_biography', 'with_book')->create();

		$author = factory(Author::class)
			->states('with_biography', 'with_book')->create();

		$author2 = factory(Author::class)
			->states('with_biography', 'with_book')->create();

		$response = $this->actingAs($user)
			->get(route('authors.merge', [
				'authors' => [$main_author->id, $author->id, $author2->id]
			]));
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertOk();

		$response = $this->actingAs($user)
			->post(route('authors.merge'),
				[
					'main_author' => $main_author->id,
					'authors' => [$author->id, $author2->id]
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('authors.show', ['author' => $main_author]));

		$main_author->refresh();
		$author->refresh();
		$author2->refresh();

		$this->assertFalse($main_author->isMerged());
		$this->assertTrue($author->isMerged());
		$this->assertTrue($author2->isMerged());

		$this->assertEquals(3, $main_author->any_books()->count());
		$this->assertEquals(0, $author->any_books()->count());
		$this->assertEquals(0, $author2->any_books()->count());

		$this->assertEquals($main_author->id, $author->redirect_to_author->id);
		$this->assertEquals($main_author->id, $author2->redirect_to_author->id);

		$this->assertEquals(1, $author->activities()->count());
		$activity = $author->activities()->first();
		$this->assertEquals('merged', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);

		$this->assertEquals(1, $author2->activities()->count());
		$activity = $author2->activities()->first();
		$this->assertEquals('merged', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testMergeWithoutBooks()
	{
		$user = factory(User::class)->create();
		$user->group->merge_authors = true;
		$user->push();

		$author = factory(Author::class)
			->create();

		$illustrator = factory(Author::class)
			->create();

		$this->assertEquals(0, $author->books()->count());
		$this->assertEquals(0, $illustrator->illustrated_books()->count());

		$response = $this->actingAs($user)
			->post(route('authors.merge'),
				[
					'main_author' => $author->id,
					'authors' => [$illustrator->id]
				])
			->assertSessionHasNoErrors()
			->assertRedirect(route('authors.show', ['author' => $author]));

		$this->assertEquals(0, $author->books()->count());
		$this->assertEquals(0, $illustrator->illustrated_books()->count());
		$this->assertTrue($illustrator->fresh()->isMerged());
	}

	public function testMergeIllustratedTranslatedCompiled()
	{
		$user = factory(User::class)->create();
		$user->group->merge_authors = true;
		$user->push();

		$author = factory(Author::class)
			->states('with_book')
			->create();

		$illustrator = factory(Author::class)
			->states('with_illustrated_book')
			->create();

		$editor = factory(Author::class)
			->states('with_edited_book')
			->create();

		$translator = factory(Author::class)
			->states('with_translated_book')
			->create();

		$compiler = factory(Author::class)
			->states('with_compiled_book')
			->create();

		$this->assertEquals(1, $author->books()->count());
		$this->assertEquals(1, $editor->edited_books()->count());
		$this->assertEquals(1, $compiler->compiled_books()->count());
		$this->assertEquals(1, $illustrator->illustrated_books()->count());
		$this->assertEquals(1, $translator->translated_books()->count());

		$response = $this->actingAs($user)
			->post(route('authors.merge'),
				[
					'main_author' => $author->id,
					'authors' => [$illustrator->id, $editor->id, $translator->id, $compiler->id]
				])
			->assertSessionHasNoErrors()
			->assertRedirect(route('authors.show', ['author' => $author]));

		$this->assertEquals(1, $author->books()->count());
		$this->assertEquals(1, $author->translated_books()->count());
		$this->assertEquals(1, $author->edited_books()->count());
		$this->assertEquals(1, $author->compiled_books()->count());
		$this->assertEquals(1, $author->illustrated_books()->count());

		$this->assertEquals(0, $translator->translated_books()->count());
		$this->assertEquals(0, $editor->edited_books()->count());
		$this->assertEquals(0, $compiler->compiled_books()->count());
		$this->assertEquals(0, $illustrator->illustrated_books()->count());

		$this->assertFalse($author->fresh()->isMerged());
		$this->assertTrue($illustrator->fresh()->isMerged());
		$this->assertTrue($editor->fresh()->isMerged());
		$this->assertTrue($translator->fresh()->isMerged());
		$this->assertTrue($compiler->fresh()->isMerged());
	}

	public function testMergeWithBiography()
	{
		$user = factory(User::class)->create();
		$user->group->merge_authors = true;
		$user->push();

		$main_author = factory(Author::class)
			->states('with_book')->create();

		$author = factory(Author::class)
			->states('with_biography', 'with_book')->create();

		$biography = $author->biography;

		$response = $this->actingAs($user)
			->post(route('authors.merge'),
				[
					'main_author' => $main_author->id,
					'authors' => [$author->id]
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('authors.show', ['author' => $main_author]));

		$main_author->refresh();
		$author->refresh();

		$this->assertNotNull($main_author->biography);
		$this->assertNotNull($author->biography);
		$this->assertEquals($biography->text, $main_author->biography->text);
	}

	public function testMergeWithBookBelongsBothAuthors()
	{
		$user = factory(User::class)->create();
		$user->group->merge_authors = true;
		$user->push();

		$main_author = factory(Author::class)
			->states('with_book')->create();

		$author = factory(Author::class)->create();

		$main_author->books->first()->writers()->syncWithoutDetaching([$author->id]);

		$this->assertEquals(1, $main_author->fresh()->any_books()->count());
		$this->assertEquals(1, $author->fresh()->any_books()->count());

		$response = $this->actingAs($user)
			->post(route('authors.merge'),
				[
					'main_author' => $main_author->id,
					'authors' => [$author->id]
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('authors.show', ['author' => $main_author]));

		$main_author->refresh();
		$author->refresh();

		$this->assertEquals(1, $main_author->any_books()->count());
		$this->assertEquals(0, $author->any_books()->count());
	}

	public function testMergeWithAuthorSentForReview()
	{
		$user = factory(User::class)->create();
		$user->group->merge_authors = true;
		$user->push();

		$main_author = factory(Author::class)
			->states('with_book')->create();

		$author = factory(Author::class)
			->states('with_book')->create();
		$author->statusSentForReview();
		$author->save();

		$book = $author->books()->first();
		$book->statusSentForReview();
		$book->save();

		$response = $this->actingAs($user)
			->post(route('authors.merge'),
				[
					'main_author' => $main_author->id,
					'authors' => [$author->id]
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('authors.show', ['author' => $main_author]));

		$main_author->refresh();
		$author->refresh();

		$this->assertFalse($main_author->isMerged());
		$this->assertTrue($author->isMerged());

		$this->assertEquals(2, $main_author->any_books()->any()->count());
		$this->assertEquals(0, $author->any_books()->any()->count());
	}

	public function testMergeWithBookVoteAndComment()
	{
		$user = factory(User::class)->create();
		$user->group->merge_authors = true;
		$user->push();

		$main_author = factory(Author::class)
			->create()->fresh();

		$author = factory(Author::class)
			->states(['with_book_vote', 'with_book_comment'])
			->create()->fresh();

		$vote_average = $author->vote_average;
		$votes_count = $author->votes_count;
		$comments_count = $author->comments_count;

		$response = $this->actingAs($user)
			->post(route('authors.merge'),
				[
					'main_author' => $main_author->id,
					'authors' => [$author->id]
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('authors.show', ['author' => $main_author]));

		$main_author->refresh();
		$author->refresh();

		$this->assertEquals(0, $author->vote_average);
		$this->assertEquals(0, $author->votes_count);
		$this->assertEquals(0, $author->comments_count);

		$this->assertEquals($vote_average, $main_author->vote_average);
		$this->assertEquals($votes_count, $main_author->votes_count);
		$this->assertEquals($comments_count, $main_author->comments_count);
	}

	public function testMakeAccepted()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->create();
		$user->group->check_books = true;
		$user->push();

		$author = factory(Author::class)->create();
		$author->statusSentForReview();
		$author->save();

		$this->get(route('authors.show', $author))
			->assertOk()
			->assertSeeText(__('author.on_review_please_wait'));

		$response = $this->actingAs($user)
			->followingRedirects()
			->get(route('authors.make_accepted', $author));
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertOk()
			->assertSeeText(__('author.published'))
			->assertDontSeeText(__('author.on_review_please_wait'));

		$author->refresh();

		$this->assertTrue($author->isAccepted());

		$this->assertEquals(1, $author->activities()->count());
		$activity = $author->activities()->first();
		$this->assertEquals('make_accepted', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testPolicyMakeAccepted()
	{
		$user = factory(User::class)->create();

		$author = factory(Author::class)->create();
		$author->statusAccepted();
		$author->save();
		$user->refresh();

		$this->assertFalse($user->can('makeAccepted', $author));

		$user->group->check_books = true;
		$user->push();
		$user->refresh();

		$this->assertFalse($user->can('makeAccepted', $author));

		$author->statusSentForReview();
		$author->save();
		$author->refresh();

		$this->assertTrue($user->can('makeAccepted', $author));
	}

	public function testView()
	{
		$author = factory(Author::class)
			->create();

		$book = factory(Book::class)->create();
		$translated_book = factory(Book::class)->create();
		$illustrated_book = factory(Book::class)->create();

		$author->books()->sync([$book->id]);
		$author->translated_books()->sync([$book->id]);
		$author->illustrated_books()->sync([$book->id]);
		$author->push();
		$author->save();

		$this->get(route('books.show', $book))
			->assertOk();

		$this->get(route('books.show', $translated_book))
			->assertOk();

		$this->get(route('books.show', $illustrated_book))
			->assertOk();

		$this->get(route('authors.show', $author))
			->assertOk();

		$author->refresh();

		$this->assertEquals(3, $author->view_day);
		$this->assertEquals(3, $author->view_week);
		$this->assertEquals(3, $author->view_month);
		$this->assertEquals(3, $author->view_year);
		$this->assertEquals(3, $author->view_all);
		$this->assertNotNull($author->view_updated_at);

		Carbon::setTestNow(now()->addDay());

		Artisan::call('clear:book_view_counts_period', ['period' => 'day']);
		Artisan::call('clear:book_view_ip');

		$this->get(route('books.show', $book))
			->assertOk();

		$this->get(route('books.show', $translated_book))
			->assertOk();

		$this->get(route('books.show', $illustrated_book))
			->assertOk();

		$this->get(route('authors.show', $author))
			->assertOk();

		$author->refresh();

		$this->assertEquals(3, $author->view_day);
		$this->assertEquals(6, $author->view_week);
		$this->assertEquals(6, $author->view_month);
		$this->assertEquals(6, $author->view_year);
		$this->assertEquals(6, $author->view_all);
		$this->assertNotNull($author->view_updated_at);
	}

	public function testCantCloseAccessIfNoPermission()
	{
		$admin = factory(User::class)
			->create();

		$author = factory(Author::class)
			->create();

		$admin->group->book_secret_hide_set = false;
		$admin->push();

		$this->assertFalse($admin->can('booksCloseAccess', $author));
	}

	public function testCanCloseAccessIfHasPermission()
	{
		$admin = factory(User::class)
			->create();

		$author = factory(Author::class)
			->create();

		$admin->group->book_secret_hide_set = true;
		$admin->push();

		$this->assertTrue($admin->can('booksCloseAccess', $author));
	}

	public function testBooksCloseAccess()
	{
		$admin = factory(User::class)
			->create();
		$admin->group->book_secret_hide_set = true;
		$admin->push();

		$author = factory(Author::class)
			->create();

		$book = factory(Book::class)->create();
		$translated_book = factory(Book::class)->create();
		$illustrated_book = factory(Book::class)->create();

		$author->books()->sync([$book->id]);
		$author->translated_books()->sync([$book->id]);
		$author->illustrated_books()->sync([$book->id]);
		$author->push();
		$author->save();

		$books = $author->any_books()->get();

		foreach ($books as $book) {
			$this->assertTrue($book->isReadAccess());
			$this->assertTrue($book->isDownloadAccess());
		}

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('authors.books.close_access', $author))
			->assertOk()
			->assertSeeText(__('author.books_access_closed'));

		$books = $author->any_books()->get();

		foreach ($books as $book) {
			$this->assertFalse($book->isReadOrDownloadAccess());
		}
	}

	public function testListReadLaterHttp()
	{
		$author_status = factory(AuthorStatus::class)
			->states('read_later')
			->create();

		$this->actingAs($author_status->user)
			->get(route('users.authors.read_later', ['user' => $author_status->user]))
			->assertOk()
			->assertSeeText($author_status->author->name);
	}

	public function testShowPrivateHttp()
	{
		$author = factory(Author::class)
			->states('private')
			->create();

		$this->assertTrue($author->isPrivate());

		$this->get(route('authors.show', ['author' => $author]))
			->assertForbidden();
	}

	public function testBookVotesHttp()
	{
		$author = factory(Author::class)
			->states('with_book_vote')
			->create();

		$book = $author->books()->get()->first();
		$vote = $book->votes()->get()->first();
		$user = $vote->create_user;

		$this->get(route('authors.books_votes', ['author' => $author]))
			->assertOk()
			->assertSeeText($user->nick);
	}

	public function testBookVotesHttpIfUserDeleted()
	{
		$author = factory(Author::class)
			->states('with_book_vote')
			->create();

		$book = $author->books()->get()->first();
		$vote = $book->votes()->get()->first();
		$user = $vote->create_user;

		$user->delete();

		$this->get(route('authors.books_votes', ['author' => $author]))
			->assertOk()
			->assertSeeText(__('User is not found'));
	}

	public function testEditDeletedHttp()
	{
		$user = factory(User::class)->create();
		$user->group->author_edit = true;
		$user->push();

		$author = factory(Author::class)
			->states('with_photo')
			->create()
			->fresh();

		$this->assertEquals(1, $author->photos()->count());
		$this->assertNotNull($author->photo);

		$author->delete();

		$response = $this->actingAs($user)
			->get(route('authors.edit', ['author' => $author]))
			->assertOk();
	}

	public function testIsOnlineIfManagerAccepted()
	{
		config(['litlife.user_last_activity' => 5]);

		$author = factory(Author::class)
			->states('with_author_manager', 'accepted')
			->create()
			->fresh();

		$user = $author->managers->first()->user;

		Carbon::setTestNow(now()->addMinutes(5)->addMinute());

		$this->assertFalse($user->isOnline());
		$this->assertFalse($author->isOnline());

		$user->last_activity_at = now();
		$user->save();

		$this->assertTrue($user->isOnline());
		$this->assertTrue($author->isOnline());

		Carbon::setTestNow(now()->addMinutes(5)->addMinute());

		$this->assertFalse($user->isOnline());
		$this->assertFalse($author->isOnline());
	}

	public function testIsOnlineIfManagerOnReview()
	{
		config(['litlife.user_last_activity' => 5]);

		$author = factory(Author::class)
			->states('with_author_manager_on_review', 'accepted')
			->create()
			->fresh();

		$manager = $author->managers->first();

		$this->assertTrue($manager->isSentForReview());

		$user = $manager->user;

		Carbon::setTestNow(now()->addMinutes(5)->addMinute());

		$this->assertFalse($user->isOnline());
		$this->assertFalse($author->isOnline());

		$user->last_activity_at = now();
		$user->save();

		$this->assertTrue($user->isOnline());
		$this->assertFalse($author->isOnline());
	}

	public function testDeleteHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->states('admin')->create();

		$author = factory(Author::class)->create();

		$this->actingAs($admin)
			->get(route('authors.delete', $author))
			->assertRedirect(route('authors.show', $author));

		$author->refresh();

		$this->assertSoftDeleted($author);

		$this->assertEquals(1, $author->activities()->count());
		$activity = $author->activities()->first();
		$this->assertEquals('deleted', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);

	}

	public function testRestoreHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->states('admin')->create();

		$author = factory(Author::class)->create();
		$author->delete();

		$this->actingAs($admin)
			->get(route('authors.delete', $author))
			->assertRedirect(route('authors.show', $author));

		$author->refresh();

		$this->assertFalse($author->trashed());

		$this->assertEquals(1, $author->activities()->count());
		$activity = $author->activities()->first();
		$this->assertEquals('restored', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testAllBooksLinks()
	{
		Storage::fake('old');

		$user = factory(User::class)->create();

		$book = factory(Book::class)
			->states('with_writer')
			->create();

		$file = factory(BookFile::class)
			->states('txt', 'storage_old')
			->create(['book_id' => $book->id]);

		$url = route('books.files.show', ['book' => $file->book, 'fileName' => $file->name]);

		$author = $book->authors()->first();

		$this->assertNotNull($author);

		$response = $this->actingAs($user)
			->get(route('authors.books.files.urls', ['author' => $author]))
			->assertOk()
			->assertSeeText($url)
			->assertHeader('Content-Type', 'text/plain; charset=UTF-8');

		$this->assertRegExp('/attachment\;\ filename\=\"' . __('author.links_to_books') . '([[:print:]]+)([0-9]+)\.txt\"/iu', $response->headers->get('content-disposition'));
	}

	public function testAllBooksLinksDontAddIfBookClosedForDownload()
	{
		Storage::fake('old');

		$user = factory(User::class)->create();

		$book = factory(Book::class)->states('with_writer')->create();

		$file = factory(BookFile::class)
			->states('txt', 'storage_old')
			->create(['book_id' => $book->id]);

		$url = route('books.files.show', ['book' => $file->book, 'fileName' => $file->name]);

		$author = $book->authors()->first();

		$this->assertNotNull($author);

		$response = $this->actingAs($user)
			->get(route('authors.books.files.urls', ['author' => $author]))
			->assertOk()
			->assertSeeText($url);

		$book->downloadAccessDisable();
		$book->save();

		$response = $this->actingAs($user)
			->get(route('authors.books.files.urls', ['author' => $author]))
			->assertOk()
			->assertDontSeeText($url);
	}

	public function testDetachUserAuthorGroupOnAuthorDelete()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$manager = factory(Manager::class)
			->states('author', 'accepted')
			->create();

		$user = $manager->user;
		$author = $manager->manageable;

		$user->attachUserGroupByNameIfExists('Автор');

		$this->assertNotNull($user->groups()->disableCache()->whereName('Автор')->first());

		$this->actingAs($admin)
			->get(route('authors.delete', ['author' => $author]))
			->assertRedirect();

		$user->refresh();
		$author->refresh();

		$this->assertSoftDeleted($author);

		$this->assertNull($user->groups()->disableCache()->whereName('Автор')->first());
	}

	public function testIsBookAttributeTitleAuthorsHelperUpdateAfterAuthorUpdate()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$author = factory(Author::class)
			->states('with_book')
			->create();

		$book = $author->books()->first();

		$last_name = uniqid();

		$post = $author->toArray();
		$post['last_name'] = $last_name;

		$this->actingAs($user)
			->patch(route('authors.update', $author), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$author->refresh();
		$book->refresh();

		$this->assertEquals($last_name, $author->last_name);

		$expected = mb_strtolower(trim($book->title));
		$expected = mb_str_replace('ё', 'е', $expected);

		$this->assertEquals($expected, $book->title_search_helper);
	}

	public function testBookAuthorPivotType()
	{
		$author = factory(Author::class)
			->states(['with_illustrated_book'])
			->create();

		$book = $author->illustrated_books()->first();

		$author = $book->illustrators()->first();

		$this->assertNotNull($author);
		$this->assertNotNull($author->pivot);
		$this->assertEquals(4, $author->pivot->type);
		$this->assertEquals(AuthorEnum::Illustrator, $author->pivot->type);
		$this->assertEquals('Illustrator', $author->pivot->getTypeKey());
	}

	public function testDontSeeWrittenMinorBooks()
	{
		$author = factory(Author::class)->create();

		$mainBook = factory(Book::class)
			->states('with_minor_book')->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$author->books()->sync([$mainBook->id, $minorBook->id]);

		$this->get(route('authors.show', $author))
			->assertOk()
			->assertSeeText($mainBook->title)
			->assertDontSeeText($minorBook->title)
			->assertViewHas('books_count', 1);
	}

	public function testSeeTranslatedMinorBooks()
	{
		$author = factory(Author::class)->create();

		$mainBook = factory(Book::class)
			->states('with_minor_book')->create();

		$minorBook = $mainBook->groupedBooks()->first();

		$author->translated_books()->sync([$mainBook->id, $minorBook->id]);

		$this->get(route('authors.show', $author))
			->assertOk()
			->assertSeeText($mainBook->title)
			->assertSeeText($minorBook->title)
			->assertViewHas('books_count', 2);
	}

	public function testSeeBookNotInGroup()
	{
		$author = factory(Author::class)->create();

		$mainBook = factory(Book::class)->create();

		$author->books()->sync([$mainBook->id]);

		$this->get(route('authors.show', $author))
			->assertOk()
			->assertSeeText($mainBook->title)
			->assertViewHas('books_count', 1);
	}

	public function testCreateAuthorAverageRatingDBRecord()
	{
		$author = factory(Author::class)->make();
		$author->save();
		$author->refresh();

		$this->assertDatabaseHas('author_average_rating_for_periods', [
			'author_id' => $author->id
		]);
	}

	public function testConvertAllBooksInTheOldFormatToTheNewOne()
	{
		$book = factory(Book::class)
			->states('with_writer', 'with_source')
			->create(['online_read_new_format' => false]);

		$author = $book->authors()->first();
		$file = $book->files()->first();

		$this->assertNotNull($author);
		$this->assertNotNull($file);

		$author->convertAllBooksInTheOldFormatToTheNewOne();

		$file->refresh();
		$book->refresh();

		$this->assertTrue($book->parse->isWait());
		$this->assertTrue($book->parse->isParseOnlyPages());
	}
}
