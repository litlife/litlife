<?php

namespace Tests\Feature\Author;

use App\Author;
use App\AuthorBiography;
use App\User;
use Tests\TestCase;

class AuthorBiographyTest extends TestCase
{
	public function testAutoParagraph()
	{
		$bio = factory(AuthorBiography::class)
			->create(['text' => '<p>текст</p>']);
		$bio->refresh();

		$this->assertEquals('<p>текст</p>', $bio->text);
	}

	public function testUrl()
	{
		$text = 'текст http://example.com текст';

		$authorBiography = factory(AuthorBiography::class)
			->create(['text' => $text]);

		$authorBiography->refresh();

		$this->assertEquals('<p>текст <a href="/away?url=http%3A%2F%2Fexample.com">http://example.com</a> текст</p>', $authorBiography->text);
	}

	public function testAuthorEditBiographyHttp()
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
}
