<?php

namespace Tests\Feature\Artisan;

use App\Manager;
use Tests\TestCase;

class AttachAuthorGroupToUsersTest extends TestCase
{
	public function testAttached()
	{
		$manager = factory(Manager::class)
			->states('accepted', 'author', 'character_author')
			->create();

		$user = $manager->user;

		$this->assertNull($user->groups()->whereName('Автор')->first());

		$this->artisan('user:attach_author_group_to_users', ['id' => $manager->id])
			->expectsOutput('Manager: ' . $manager->id . '')
			->expectsOutput('Присоединяем группу Автор к пользователю ' . $user->id . '')
			->assertExitCode(0);

		$manager->refresh();
		$user->refresh();

		$this->assertEquals(2, $user->groups()->count());
		$this->assertNotNull($user->groups()->whereName('Автор')->first());
	}

	public function testIfCharacterEditor()
	{
		$manager = factory(Manager::class)
			->states('accepted', 'character_editor')
			->create();

		$user = $manager->user;

		$this->assertNull($user->groups()->whereName('Автор')->first());

		$this->artisan('user:attach_author_group_to_users', ['id' => $manager->id])
			->assertExitCode(0);

		$manager->refresh();
		$user->refresh();

		$this->assertNull($user->groups()->whereName('Автор')->first());
	}

	public function testIfNotAccepted()
	{
		$manager = factory(Manager::class)
			->states('on_review', 'character_author')
			->create();

		$user = $manager->user;

		$this->assertNull($user->groups()->whereName('Автор')->first());

		$this->artisan('user:attach_author_group_to_users', ['id' => $manager->id])
			->assertExitCode(0);

		$manager->refresh();
		$user->refresh();

		$this->assertNull($user->groups()->whereName('Автор')->first());
	}

	public function testIfAuthorDeletedNotAttach()
	{
		$manager = factory(Manager::class)
			->states('accepted', 'author', 'character_author')
			->create();

		$user = $manager->user;
		$author = $manager->manageable;

		$author->delete();

		$this->assertSoftDeleted($author);

		$this->assertNull($user->groups()->disableCache()->whereName('Автор')->first());

		$this->artisan('user:attach_author_group_to_users', ['id' => $manager->id])
			->assertExitCode(0);

		$manager->refresh();
		$user->refresh();

		$this->assertNull($user->groups()->disableCache()->whereName('Автор')->first());
	}

	public function testDontAttachIfAuthorMerged()
	{
		$manager = factory(Manager::class)
			->states('accepted', 'author', 'character_author')
			->create();

		$user = $manager->user;
		$author = $manager->manageable;

		$author->merged_at = now();
		$author->save();
		$author->refresh();

		$this->assertTrue($author->isMerged());

		$this->assertNull($user->groups()->disableCache()->whereName('Автор')->first());

		$this->artisan('user:attach_author_group_to_users', ['id' => $manager->id])
			->assertExitCode(0);

		$manager->refresh();
		$user->refresh();

		$this->assertNull($user->groups()->disableCache()->whereName('Автор')->first());
	}
}
