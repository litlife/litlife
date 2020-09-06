<?php

namespace Tests\Feature\Book\Keyword;

use App\Book;
use App\BookKeyword;
use App\Enums\StatusEnum;
use App\Keyword;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class BookKeywordTest extends TestCase
{
	public function testSearchHttp()
	{
		$book_keyword = factory(BookKeyword::class)->create();

		$response = $this->json('get', route('books.keywords.search'),
			['q' => $book_keyword->keyword->text])
			->assertOk();

		$response->assertJsonFragment(['text' => $book_keyword->keyword->text]);
	}

	public function testSearchPartWordHttp()
	{
		$text = Str::random(8);

		$book_keyword = factory(BookKeyword::class)->create();
		$book_keyword->keyword->text = $text;
		$book_keyword->push();
		$book_keyword->refresh();

		$response = $this->json('get', route('books.keywords.search'),
			['q' => mb_substr($text, 1)])
			->assertOk();

		$response->assertJsonFragment(['text' => $book_keyword->keyword->text]);
	}

	public function testSearchDeletedKeywordHttp()
	{
		$text = Str::random(8);

		$book_keyword = factory(BookKeyword::class)->create();
		$book_keyword->keyword->text = $text;
		$book_keyword->push();

		$book_keyword->keyword->delete();

		$response = $this->json('get', route('books.keywords.search'),
			['q' => $book_keyword->keyword->text])
			->assertOk();

		$response->assertJsonMissing(['text' => $book_keyword->keyword->text]);
	}

	public function testSearchDeletedBookKeywordHttp()
	{
		$keyword = factory(Keyword::class)->create();

		$keyword->delete();

		$response = $this->json('get', route('books.keywords.search'),
			['q' => $keyword->text])
			->assertOk();

		$response->assertJsonMissing(['text' => $keyword->text]);
	}


	public function testAddExistedKeywordHttp()
	{
		$book = factory(Book::class)
			->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusAccepted();
		$keyword->save();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$keyword->text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(StatusEnum::Accepted, $book_keyword->status);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword->keyword->status);
	}

	public function testDelete()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_remove = true;
		$user->push();

		$book_keyword = factory(BookKeyword::class)->create();
		$book_keyword->statusAccepted();
		$book_keyword->save();

		$keyword = $book_keyword->keyword;

		$response = $this->actingAs($user)
			->delete(route('books.keywords.destroy',
				['book' => $book_keyword->book, 'keyword' => $book_keyword->id]))
			->assertOk();

		$this->assertNull($book_keyword->fresh());
		$this->assertNotNull($keyword->fresh());
	}

	public function testDeleteIfKeywordPrivate()
	{
		$book_keyword = factory(BookKeyword::class)
			->states('private')
			->create();

		$keyword = $book_keyword->keyword;

		$this->assertTrue($book_keyword->isPrivate());
		$this->assertTrue($book_keyword->keyword->isPrivate());

		$response = $this->actingAs($book_keyword->create_user)
			->delete(route('books.keywords.destroy',
				['book' => $book_keyword->book, 'keyword' => $book_keyword->id]))
			->assertOk();

		$this->assertNull($book_keyword->fresh());
		$this->assertNull($keyword->fresh());
	}

	public function testDeleteIfKeywordOnReview()
	{
		$admin = factory(User::class)
			->create();
		$admin->group->book_keyword_remove = true;
		$admin->push();

		$book_keyword = factory(BookKeyword::class)
			->states('on_review')
			->create();

		$keyword = $book_keyword->keyword;

		$book_keyword2 = factory(BookKeyword::class)
			->states('on_review')
			->create(['keyword_id' => $keyword->id]);

		$this->assertTrue($book_keyword->isSentForReview());
		$this->assertTrue($book_keyword2->isSentForReview());
		$this->assertTrue($book_keyword->keyword->isSentForReview());

		$response = $this->actingAs($admin)
			->delete(route('books.keywords.destroy',
				['book' => $book_keyword->book, 'keyword' => $book_keyword->id]))
			->assertOk();

		$response = $this->actingAs($admin)
			->delete(route('books.keywords.destroy',
				['book' => $book_keyword2->book, 'keyword' => $book_keyword2->id]))
			->assertOk();

		$this->assertNull($book_keyword->fresh());
		$this->assertNull($book_keyword2->fresh());
		$this->assertNull($keyword->fresh());
	}

	public function testSeeBookTitleIfKeywordSentForReview()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_moderate = true;
		$user->push();

		$book = factory(Book::class)->create();
		$book->statusSentForReview();
		$book->save();
		$book->refresh();

		$book_keyword = factory(BookKeyword::class)->create(['book_id' => $book->id]);
		$book_keyword->statusSentForReview();
		$book_keyword->save();

		$response = $this->actingAs($user)
			->get(route('book_keywords.on_moderation'))
			->assertOk()
			->assertSeeText($book->title)
			->assertSeeText($book_keyword->keyword->text);
	}

	public function testVoteUpRemoveVoteUp()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_vote = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_keyword')
			->create();

		$book_keyword = $book->book_keywords->first();

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '1']))
			->assertStatus(201)
			->assertJsonFragment([
				'vote' => '1',
			]);

		$book_keyword->refresh();

		$this->assertEquals(1, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(1, $vote->vote);

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '1']))
			->assertStatus(200)
			->assertJsonFragment([
				'vote' => 0
			]);

		$book_keyword->refresh();

		$this->assertEquals(0, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(0, $vote->vote);
	}

	public function testVoteUpVoteDown()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_vote = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_keyword')
			->create();

		$book_keyword = $book->book_keywords->first();

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '1']))
			->assertStatus(201)
			->assertJsonFragment([
				'vote' => '1',
			]);

		$book_keyword->refresh();

		$this->assertEquals(1, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(1, $vote->vote);

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '-1']))
			->assertStatus(200)
			->assertJsonFragment([
				'vote' => '-1'
			]);

		$book_keyword->refresh();

		$this->assertEquals(-1, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(-1, $vote->vote);
	}

	public function testVoteDownRemoveVoteDown()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_vote = true;
		$user->push();

		$book = factory(Book::class)
			->states('with_keyword')
			->create();

		$book_keyword = $book->book_keywords->first();

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '-1']))
			->assertStatus(201)
			->assertJsonFragment([
				'vote' => '-1',
			]);

		$book_keyword->refresh();

		$this->assertEquals(-1, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(-1, $vote->vote);

		$response = $this->actingAs($user)
			->json('GET', route('books.keywords.vote',
				['book' => $book, 'keyword' => $book_keyword->id, 'vote' => '-1']))
			->assertStatus(200)
			->assertJsonFragment([
				'vote' => 0
			]);

		$book_keyword->refresh();

		$this->assertEquals(0, $book_keyword->rating);
		$this->assertEquals(1, $book_keyword->votes()->count());

		$vote = $book_keyword->votes()->first();

		$this->assertEquals($book_keyword->id, $vote->book_keyword_id);
		$this->assertEquals($user->id, $vote->create_user_id);
		$this->assertEquals(0, $vote->vote);
	}

	public function testDeletePolicy()
	{
		$user = factory(User::class)
			->create();

		$book_keyword = factory(BookKeyword::class)
			->create();

		$this->assertFalse($user->can('delete', $book_keyword));

		$user->group->book_keyword_remove = true;
		$user->push();

		$this->assertTrue($user->can('delete', $book_keyword));
	}

	public function testDeletePolicyIfPrivate()
	{
		$user = factory(User::class)
			->create();

		$book_keyword = factory(BookKeyword::class)
			->create();

		$this->assertFalse($user->can('delete', $book_keyword));

		$book_keyword->book->statusAccepted();
		$book_keyword->statusAccepted();
		$book_keyword->create_user_id = $user->id;
		$book_keyword->push();

		$this->assertFalse($user->can('delete', $book_keyword));

		$book_keyword->book->statusPrivate();
		$book_keyword->statusPrivate();
		$book_keyword->push();

		$this->assertTrue($user->can('delete', $book_keyword));
	}

	public function testCantDeleteIfNoPermissions()
	{
		$user = factory(User::class)->create();

		$book_keyword = factory(BookKeyword::class)
			->create();

		$this->assertFalse($user->can('delete', $book_keyword));
	}

	public function testCanDeleteIfHasPermissions()
	{
		$user = factory(User::class)->create();
		$user->group->book_keyword_remove = true;
		$user->push();

		$book_keyword = factory(BookKeyword::class)
			->create();

		$this->assertTrue($user->can('delete', $book_keyword));
	}

	public function testCanDeleteIfOnReviewAndUserCreator()
	{
		$book_keyword = factory(BookKeyword::class)->create();
		$book_keyword->statusSentForReview();
		$book_keyword->save();

		$user = $book_keyword->create_user;

		$this->assertTrue($user->can('delete', $book_keyword));
	}

	public function testCantDeleteIfAcceptedAndUserCreator()
	{
		$book_keyword = factory(BookKeyword::class)->create();
		$book_keyword->statusAccepted();
		$book_keyword->save();

		$user = $book_keyword->create_user;

		$this->assertFalse($user->can('delete', $book_keyword));
	}

	public function testCanAttachExisted()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusAccepted();
		$keyword->save();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$keyword->text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(StatusEnum::Accepted, $book_keyword->status);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword->keyword->status);
	}

	public function testCanAttachById()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusAccepted();
		$keyword->save();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$keyword->id]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals($keyword->id, $book_keyword->keyword->id);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword->status);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword->keyword->status);
	}

	public function testCantAttachNew()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$text = Str::random(8);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$text]])
			->assertRedirect();

		$this->assertEquals(0, $book->book_keywords()->count());
	}

	public function testCanAttachNewOnCheck()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = true;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$text = Str::random(10);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(mb_ucfirst($text), $book_keyword->keyword->text);
		$this->assertTrue($book_keyword->isSentForReview());
		$this->assertTrue($book_keyword->keyword->isSentForReview());
	}

	public function testCanAttachNewAccepted()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = true;
		$user->group->book_keyword_moderate = true;
		$user->push();

		$text = Str::random(8);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(mb_ucfirst($text), $book_keyword->keyword->text);
		$this->assertTrue($book_keyword->isAccepted());
		$this->assertTrue($book_keyword->keyword->isAccepted());
		$this->assertEquals($book_keyword->book_id, $book_keyword->origin_book_id);
	}

	public function testCanAttachNewAccepted2()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = false;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = true;
		$user->push();

		$text = Str::random(10);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(mb_ucfirst($text), $book_keyword->keyword->text);
		$this->assertTrue($book_keyword->isAccepted());
		$this->assertTrue($book_keyword->keyword->isAccepted());
	}

	public function testCantAttachNewToPrivateBook()
	{
		$book = factory(Book::class)
			->states('with_create_user', 'private')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_add = false;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$text = Str::random(7);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$text]])
			->assertRedirect();

		$this->assertEquals(0, $book->book_keywords()->count());
	}

	public function testCanAttachExistedToPrivateBook()
	{
		$book = factory(Book::class)
			->states('with_create_user', 'private')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_add = false;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusAccepted();
		$keyword->save();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$keyword->text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertTrue($book_keyword->isPrivate());
		$this->assertTrue($book_keyword->keyword->isAccepted());
	}

	public function testCantAttachExistedPrivateToPrivateBook()
	{
		$book = factory(Book::class)
			->states('with_create_user', 'private')
			->create();

		$user = $book->create_user;
		$user->group->book_keyword_add = false;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusPrivate();
		$keyword->save();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$keyword->text]])
			->assertRedirect();

		$book_keyword = $book->book_keywords()->first();

		$this->assertEquals(0, $book->book_keywords()->count());
	}

	public function testAttachExistedAndNew()
	{
		$book = factory(Book::class)->create();
		$book->statusAccepted();
		$book->save();

		$user = factory(User::class)->create();
		$user->group->book_keyword_add = true;
		$user->group->book_keyword_add_new_with_check = false;
		$user->group->book_keyword_moderate = false;
		$user->push();

		$keyword = factory(Keyword::class)->create();
		$keyword->statusAccepted();
		$keyword->save();

		$new_text = Str::random(8);

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$new_text, $keyword->text]])
			->assertRedirect();

		$book_keyword_existed = $book->book_keywords()
			->where('keyword_id', $keyword->id)
			->first();

		$this->assertEquals(StatusEnum::Accepted, $book_keyword_existed->status);
		$this->assertEquals(StatusEnum::Accepted, $book_keyword_existed->keyword->status);

		$book_keyword_new = $book->book_keywords()
			->where('keyword_id', '!=', $keyword->id)
			->first();

		$this->assertNull($book_keyword_new);
	}

	public function testIfKeywordForceDeleted()
	{
		$book = factory(Book::class)
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$keyword = factory(Keyword::class)
			->create();

		$id = $keyword->id;

		$keyword->forceDelete();

		$response = $this->actingAs($user)
			->post(route('books.keywords.store', ['book' => $book]),
				['keywords' => [$id]])
			->assertSessionHasNoErrors()
			->assertRedirect();
	}

	public function testShowBookKeywordsIfBookDeleted()
	{
		$bookKeyword = factory(BookKeyword::class)
			->create();

		$book = $bookKeyword->book;

		$book->delete();

		$user = factory(User::class)
			->states('admin')
			->create();

		$response = $this->actingAs($user)
			->get(route('books.keywords.index', ['book' => $book]))
			->assertOk();
	}

	public function testShowBookEditIfKeywordDeleted()
	{
		$bookKeyword = factory(BookKeyword::class)
			->create();

		$book = $bookKeyword->book;

		$bookKeyword->keyword->delete();

		$user = factory(User::class)
			->states('admin')
			->create();

		$response = $this->actingAs($user)
			->get(route('books.edit', ['book' => $book]))
			->assertOk();

		$response = $this->actingAs($user)
			->get(route('books.keywords.index', ['book' => $book]))
			->assertOk();
	}
}
