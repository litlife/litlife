<?php

namespace Tests\Feature\Author\Manager;

use App\Author;
use App\Manager;
use App\User;
use Tests\TestCase;

class AuthorManagerCreateTest extends TestCase
{
	public function testAttachUserToAuthor()
	{
		$admin = factory(User::class)->create();
		$admin->group->moderator_add_remove = true;
		$admin->push();

		$author = factory(Author::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($admin)
			->post(route('authors.managers.store', ['author' => $author->id]), [
				'user_id' => $user->id,
				'character' => 'author'
			])
			->assertRedirect()
			->assertSessionHasNoErrors();

		$manager = $author->managers()->first();

		$this->assertNotNull($manager);
		$this->assertEquals($user->id, $manager->user_id);
	}

	public function testAttachUserToAuthorIfOtherUserAlreadyAttachedAsAuthor()
	{
		$admin = factory(User::class)->create();
		$admin->group->moderator_add_remove = true;
		$admin->push();

		$author = factory(Author::class)
			->create();

		$user = factory(User::class)
			->create();

		$manager = factory(Manager::class)
			->create([
				'create_user_id' => $admin->id,
				'character' => 'author',
				'manageable_id' => $author->id,
			]);

		$response = $this->actingAs($admin)
			->post(route('authors.managers.store', ['author' => $author->id]), [
				'user_id' => $user->id,
				'character' => 'author'
			])
			->assertRedirect();

		//dump(session('errors'));

		$response->assertSessionHasErrors(['user_id' => __('The author has already been verified. Delete the other verification to add a new one')]);

		$count = $author->managers()->count();

		$this->assertEquals(1, $count);
	}

	public function testAttachAuthorUserGroupOnAttach()
	{
		$admin = factory(User::class)->states('admin')->create();

		$author = factory(Author::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($admin)
			->post(route('authors.managers.store', ['author' => $author->id]), [
				'user_id' => $user->id,
				'character' => 'author'
			])
			->assertRedirect()
			->assertSessionHasNoErrors();

		$user->refresh();

		$this->assertEquals('Автор', $user->groups()->disableCache()->whereName('Автор')->first()->name);
	}
}
