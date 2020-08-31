<?php

namespace Tests\Feature\Author;

use App\Author;
use App\AuthorRepeat;
use App\User;
use Tests\TestCase;

class AuthorRepeatTest extends TestCase
{


	public function testCreateHttp()
	{
		$user = factory(User::class)
			->create();
		$user->group->author_repeat_report_add = true;
		$user->push();

		$author = factory(Author::class)->create();
		$author->statusAccepted();
		$author->save();

		$author2 = factory(Author::class)->create();
		$author2->statusAccepted();
		$author2->save();

		$author3 = factory(Author::class)->create();
		$author3->statusAccepted();
		$author3->save();

		$text = $this->faker->sentence();

		$this->actingAs($user)
			->post(route('author_repeats.store'), [
				'authors' => [$author->id, $author2->id, $author3->id],
				'comment' => $text
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$author_repeat = $user->created_author_repeats()->first();

		$this->assertNotNull($author_repeat);

		$authors = $author_repeat->authors()->orderBy('id', 'asc')->get();

		$this->assertEquals($author->id, $authors[0]->id);
		$this->assertEquals($author2->id, $authors[1]->id);
		$this->assertEquals($author3->id, $authors[2]->id);
		$this->assertEquals($text, $author_repeat->comment);

		$this->actingAs($user)
			->get(route('author_repeats.index'))
			->assertOk()
			->assertSeeText($authors[0]->name)
			->assertSeeText($authors[1]->name)
			->assertSeeText($authors[2]->name)
			->assertSeeText($author_repeat->comment);
	}

	public function testEditHttp()
	{
		$user = factory(User::class)
			->create();
		$user->group->author_repeat_report_edit = true;
		$user->push();

		$author = factory(Author::class)->create();
		$author->statusAccepted();
		$author->save();

		$author2 = factory(Author::class)->create();
		$author2->statusAccepted();
		$author2->save();

		$author3 = factory(Author::class)->create();
		$author3->statusAccepted();
		$author3->save();

		$text = $this->faker->sentence();

		$authorRepeat = new AuthorRepeat;
		$authorRepeat->create_user()->associate($user);
		$authorRepeat->save();
		$authorRepeat->authors()->attach($author->id);
		$authorRepeat->authors()->attach($author2->id);
		$authorRepeat->authors()->attach($author3->id);

		$this->assertEquals(3, $authorRepeat->authors()->count());

		$this->actingAs($user)
			->get(route('author_repeats.index'))
			->assertOk()
			->assertSeeText(__('common.edit'));

		$text = $this->faker->sentence();

		$this->actingAs($user)
			->get(route('author_repeats.edit', ['author_repeat' => $authorRepeat->id]))
			->assertOk();

		$this->actingAs($user)
			->patch(route('author_repeats.update', ['author_repeat' => $authorRepeat->id]), [
				'authors' => [$author2->id, $author3->id],
				'comment' => $text
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$authorRepeat->refresh();

		$authors = $authorRepeat->authors()->orderBy('id', 'asc')->get();

		$this->assertEquals(2, $authorRepeat->authors()->count());
		$this->assertEquals($author2->id, $authors[0]->id);
		$this->assertEquals($author3->id, $authors[1]->id);
		$this->assertEquals($text, $authorRepeat->comment);


	}

	public function testDeleteHttp()
	{
		$user = factory(User::class)
			->create();
		$user->group->author_repeat_report_delete = true;
		$user->push();

		$author = factory(Author::class)->create();
		$author->statusAccepted();
		$author->save();

		$author2 = factory(Author::class)->create();
		$author2->statusAccepted();
		$author2->save();

		$author3 = factory(Author::class)->create();
		$author3->statusAccepted();
		$author3->save();

		$authorRepeat = new AuthorRepeat;
		$authorRepeat->create_user()->associate($user);
		$authorRepeat->save();
		$authorRepeat->authors()->attach($author->id);
		$authorRepeat->authors()->attach($author2->id);
		$authorRepeat->authors()->attach($author3->id);

		$this->actingAs($user)
			->get(route('author_repeats.index'))
			->assertOk()
			->assertSeeText(__('common.delete'));

		$this->actingAs($user)
			->get(route('author_repeats.delete', ['author_repeat' => $authorRepeat->id]))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$authorRepeat->refresh();

		$this->assertTrue($authorRepeat->trashed());
	}

	public function testMergeButton()
	{
		$user = factory(User::class)->create();
		$user->group->author_repeat_report_add = true;
		$user->push();

		$admin = factory(User::class)->create();
		$admin->group->merge_authors = true;
		$admin->group->author_repeat_report_delete = true;
		$admin->push();

		$author_repeat = factory(AuthorRepeat::class)->create();
		$author = $author_repeat->authors()->first();

		$this->actingAs($user)
			->get(route('author_repeats.index'))
			->assertOk()
			->assertSeeText($author->name)
			->assertDontSeeText(__('common.merge'))
			->assertDontSeeText(__('common.edit'))
			->assertSeeText(__('common.delete'));

		$this->actingAs($admin)
			->get(route('author_repeats.index'))
			->assertOk()
			->assertSeeText($author->name)
			->assertSeeText(__('common.merge'))
			->assertDontSeeText(__('common.edit'))
			->assertSee(__('common.delete'));
	}

	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		parent::setUp();

		foreach (AuthorRepeat::all() as $repeat) {
			$repeat->delete();
		}

		AuthorRepeat::flushCachedOnModerationCount();
	}
}
