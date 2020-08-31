<?php

namespace Tests\Feature;

use App\AdminNote;
use App\User;
use Tests\TestCase;

class AdminNoteTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testPolicy()
	{
		$user = factory(User::class)
			->states('with_user_group')
			->create();

		$admin_note = factory(AdminNote::class)
			->create();

		$this->assertFalse($user->can('create', AdminNote::class));
		$this->assertFalse($user->can('view', AdminNote::class));
		$this->assertFalse($user->can('update', $admin_note));
		$this->assertFalse($user->can('delete', $admin_note));

		$user->group->admin_comment = true;
		$user->push();

		$this->assertTrue($user->can('create', AdminNote::class));
		$this->assertTrue($user->can('view', AdminNote::class));
		$this->assertFalse($user->can('update', $admin_note));
		$this->assertFalse($user->can('delete', $admin_note));

		$admin_note_user = $admin_note->create_user;

		$this->assertFalse($admin_note_user->can('create', AdminNote::class));
		$this->assertFalse($admin_note_user->can('view', AdminNote::class));
		$this->assertFalse($admin_note_user->can('update', $admin_note));
		$this->assertFalse($admin_note_user->can('delete', $admin_note));

		$admin_note_user->group->admin_comment = true;
		$admin_note_user->push();
		$admin_note_user->refresh();

		$this->assertTrue($admin_note_user->can('create', AdminNote::class));
		$this->assertTrue($admin_note_user->can('view', AdminNote::class));
		$this->assertTrue($admin_note_user->can('update', $admin_note));
		$this->assertTrue($admin_note_user->can('delete', $admin_note));
	}

	public function testIndexHttp()
	{
		$user = factory(User::class)
			->create();

		$user->group->admin_comment = true;
		$user->push();

		$user2 = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->get(route('admin_notes.index', ['type' => 'user', 'id' => $user2->id]))
			->assertOk();
	}

	public function testCreateHttp()
	{
		$user = factory(User::class)
			->create();

		$user->group->admin_comment = true;
		$user->push();

		$user2 = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->get(route('admin_notes.create', ['type' => 'user', 'id' => $user2->id]))
			->assertOk();
	}

	public function testStoreHttp()
	{
		$user = factory(User::class)
			->create();

		$user->group->admin_comment = true;
		$user->push();

		$user2 = factory(User::class)
			->create();

		$text = $this->faker->realText(100);

		$response = $this->actingAs($user)
			->post(route('admin_notes.store', ['type' => 'user', 'id' => $user2->id]),
				['text' => $text])
			->assertRedirect()
			->assertSessionHasNoErrors();

		$admin_note = $user2->admin_notes()->first();

		$this->assertNotNull($admin_note);
		$this->assertEquals($text, $admin_note->text);

		$this->assertEquals(1, $user2->fresh()->admin_notes_count);
	}

	public function testEditHttp()
	{
		$admin_note = factory(AdminNote::class)
			->create()->fresh();

		$admin_note->create_user->group->admin_comment = true;
		$admin_note->create_user->push();

		$response = $this->actingAs($admin_note->create_user)
			->get(route('admin_notes.edit', $admin_note))
			->assertOk();
	}

	public function testUpdateHttp()
	{
		$admin_note = factory(AdminNote::class)
			->create()->fresh();

		$admin_note->create_user->group->admin_comment = true;
		$admin_note->create_user->push();

		$text = $this->faker->realText(100);

		$response = $this->actingAs($admin_note->create_user)
			->patch(route('admin_notes.update', $admin_note),
				['text' => $text])
			->assertRedirect()
			->assertSessionHasNoErrors();

		$admin_note->refresh();

		$this->assertNotNull($admin_note);
		$this->assertEquals($text, $admin_note->text);
	}

	public function testDeleteHttp()
	{
		$admin_note = factory(AdminNote::class)
			->create()->fresh();

		$admin_note->create_user->group->admin_comment = true;
		$admin_note->create_user->push();

		$text = $this->faker->realText(100);

		$response = $this->actingAs($admin_note->create_user)
			->delete(route('admin_notes.destroy', $admin_note))
			->assertRedirect()
			->assertSessionHasNoErrors();

		$admin_note->refresh();

		$this->assertTrue($admin_note->trashed());
	}

	public function testAuthRequiredIfUserGuest()
	{
		$admin_note = factory(AdminNote::class)
			->create();

		$response = $this->get(route('admin_notes.edit', $admin_note))
			->assertStatus(401);
	}
}
