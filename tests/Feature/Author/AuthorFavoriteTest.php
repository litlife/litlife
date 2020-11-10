<?php

namespace Tests\Feature\Author;

use App\Author;
use App\User;
use App\UserAuthor;
use Tests\TestCase;

class AuthorFavoriteTest extends TestCase
{
	public function testToggle()
	{
		$author = Author::factory()->create();

		$user = User::factory()->create();

		$this->actingAs($user)
			->get(route('authors.favorites.toggle', ['author' => $author]))
			->assertOk()
			->assertJson([
				'result' => 'attached',
				'added_to_favorites_count' => 1
			]);

		$user->refresh();
		$author->refresh();

		$this->assertTrue($user->is($author->addedToFavoritesUsers()->first()));
		$this->assertEquals(1, $author->added_to_favorites_count);

		$this->actingAs($user)
			->get(route('authors.favorites.toggle', ['author' => $author]))
			->assertOk()
			->assertJson([
				'result' => 'detached',
				'added_to_favorites_count' => 0
			]);

		$author->refresh();

		$this->assertEquals(0, $author->added_to_favorites_count);
	}

	public function testToggleIfAuthorDeleted()
	{
		$user_author = UserAuthor::factory()->create();

		$author = $user_author->author;
		$user = $user_author->user;

		$author->delete();

		$this->actingAs($user)
			->get(route('authors.favorites.toggle', ['author' => $author]))
			->assertOk()
			->assertJson([
				'result' => 'detached',
				'added_to_favorites_count' => 0
			]);

		$author->refresh();

		$this->assertEquals(0, $author->added_to_favorites_count);
	}
}
