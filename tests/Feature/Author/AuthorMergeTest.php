<?php

namespace Tests\Feature\Author;

use App\Author;
use App\User;
use Tests\TestCase;

class AuthorMergeTest extends TestCase
{
	public function testMerge()
	{
		config(['activitylog.enabled' => true]);

		$user = User::factory()->create();
		$user->group->merge_authors = true;
		$user->push();

		$main_author = Author::factory()->with_biography()->with_book()->create();

		$author = Author::factory()->with_biography()->with_book()->create();

		$author2 = Author::factory()->with_biography()->with_book()->create();

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

	public function testWithoutBooks()
	{
		$user = User::factory()->create();
		$user->group->merge_authors = true;
		$user->push();

		$author = Author::factory()->create();

		$illustrator = Author::factory()->create();

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

	public function testIllustratedTranslatedCompiled()
	{
		$user = User::factory()->create();
		$user->group->merge_authors = true;
		$user->push();

		$author = Author::factory()->with_book()->create();

		$illustrator = Author::factory()->with_illustrated_book()->create();

		$editor = Author::factory()->with_edited_book()->create();

		$translator = Author::factory()->with_translated_book()->create();

		$compiler = Author::factory()->with_compiled_book()->create();

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

	public function testWithBiography()
	{
		$user = User::factory()->create();
		$user->group->merge_authors = true;
		$user->push();

		$main_author = Author::factory()->with_book()->create();

		$author = Author::factory()->with_biography()->with_book()->create();

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

	public function testWithBookBelongsBothAuthors()
	{
		$user = User::factory()->create();
		$user->group->merge_authors = true;
		$user->push();

		$main_author = Author::factory()->with_book()->create();

		$author = Author::factory()->create();

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

	public function testWithAuthorSentForReview()
	{
		$user = User::factory()->create();
		$user->group->merge_authors = true;
		$user->push();

		$main_author = Author::factory()->with_book()->create();

		$author = Author::factory()->with_book()->create();
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

	public function testWithBookVoteAndComment()
	{
		$user = User::factory()->create();
		$user->group->merge_authors = true;
		$user->push();

		$main_author = Author::factory()->create()->fresh();

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
}
