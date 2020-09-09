<?php

namespace Tests\Feature\Author;

use App\Author;
use App\AuthorBiography;
use App\User;
use Tests\TestCase;

class AuthorEditTest extends TestCase
{
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

	public function testWithEmptyBiographyHttp()
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

	public function testBiographyHttp()
	{
		$user = factory(User::class)->create();
		$user->group->author_edit = true;
		$user->push();

		$author = factory(Author::class)->create();
		$author->save();

		$biography = $this->faker->realText(100);

		$response = $this->actingAs($user)
			->patch(route('authors.update', $author),
				[
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'gender' => 'male',
					'biography' => $biography
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('authors.edit', $author))
			->assertSessionHas(['success' => __('author.description_was_saved_successfully')]);

		$response = $this->get(route('authors.show', $author))
			->assertSeeText($biography);

		$biography = $this->faker->realText(100);

		$response = $this->actingAs($user)
			->patch(route('authors.update', $author),
				[
					'first_name' => $this->faker->firstName,
					'last_name' => $this->faker->lastName,
					'gender' => 'male',
					'biography' => $biography
				]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$response = $this->get(route('authors.show', $author))
			->assertSeeText($biography);
	}


	public function testDeleteBiographyIfClear()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$biography = factory(AuthorBiography::class)
			->create(['text' => '<p>текст</p>']);

		$author = $biography->author;

		$post = $author->toArray();
		$post['biography'] = '<p></p><p></p><p></p>';

		$this->actingAs($user)
			->patch(route('authors.update', $author), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$author->refresh();

		$this->assertNull($author->biography);
	}

	public function testRestoreDeletedBiography()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$biography = factory(AuthorBiography::class)
			->create(['text' => '<p>текст</p>']);

		$author = $biography->author;

		$post = $author->toArray();
		$post['biography'] = '<p>123</p>';

		$biography->delete();

		$this->actingAs($user)
			->patch(route('authors.update', $author), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$biography->refresh();

		$this->assertFalse($biography->trashed());
		$this->assertEquals($post['biography'], $biography->text);
	}

	public function testDontDeleteBiographyIfImageExists()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$biography = factory(AuthorBiography::class)
			->create(['text' => '<p>текст</p>']);

		$author = $biography->author;

		$post = $author->toArray();
		$post['biography'] = '<p><img src="https://example.com/img.png" alt="img.png" /></p>';

		$this->actingAs($user)
			->patch(route('authors.update', $author), $post)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$author->refresh();

		$this->assertNotNull($author->biography);
		$this->assertEquals($post['biography'], $author->biography->text);
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
}
