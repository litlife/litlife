<?php

namespace Tests\Feature\Author;

use App\Author;
use App\Manager;
use App\User;
use Tests\TestCase;

class AuthorDeleteRestoreTest extends TestCase
{
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
}
