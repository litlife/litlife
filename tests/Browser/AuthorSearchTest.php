<?php

namespace Tests\Browser;

use App\Author;
use App\Enums\StatusEnum;
use App\User;
use Tests\DuskTestCase;

class AuthorSearchTest extends DuskTestCase
{
	/**
	 * A Dusk test example.
	 *
	 * @return void
	 */

	public function testPrivacyInSearch()
	{
		$this->browse(function ($user_browser, $other_user_browser) {

			$author = factory(Author::class)->create([
				'nickname' => uniqid() . uniqid(),
				'status' => StatusEnum::Private
			]);

			$author = Author::any()->findOrFail($author->id);

			$other_user = factory(User::class)->create();

			$user_browser->resize(1000, 1000)
				->loginAs($author->create_user)
				->visit(route('authors', ['nick' => $author->nickname, 'order' => 'created_at_desc']))
				->assertSee($author->nickname);

			$other_user_browser->resize(1000, 1000)
				->loginAs($other_user)
				->visit(route('authors', ['nick' => $author->nickname, 'order' => 'created_at_desc']))
				->assertDontSee($author->nickname)
				->assertDontSee(__('author.deleted'))
				->assertSee(__('author.nothing_found'));
		});
	}

	public function testDeletedInSearch()
	{
		$this->browse(function ($user_browser) {

			$author = factory(Author::class)->create([
				'nickname' => uniqid() . uniqid(),
				'status' => StatusEnum::Private
			]);

			$author = Author::any()->findOrFail($author->id);

			$author->delete();

			$user_browser->resize(1000, 1000)
				->loginAs($author->create_user)
				->visit(route('authors', ['nick' => $author->nickname, 'order' => 'created_at_desc']))
				->assertDontSee($author->title)
				->assertDontSee(__('author.deleted'))
				->assertSee(__('author.nothing_found'));
		});
	}
}
