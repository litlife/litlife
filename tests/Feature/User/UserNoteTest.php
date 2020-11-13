<?php

namespace Tests\Feature\User;

use App\User;
use App\UserNote;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class UserNoteTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIndexHttp()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.notes.index', ['user' => $user]))
            ->assertOk()
            ->assertSeeText(__('user_note.nothing_found'));
    }

    public function testIndexHttpOtherUser()
    {
        $user = User::factory()->create();

        $other_user = User::factory()->create();

        $this->actingAs($other_user)
            ->get(route('users.notes.index', ['user' => $user]))
            ->assertForbidden();
    }

    public function testCreateHttp()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('users.notes.create', ['user' => $user]))
            ->assertOk();
    }

    public function testStoreHttp()
    {
        $user = User::factory()->create();

        $bb_text = '[b]test[/b]';

        $this->actingAs($user)
            ->post(route('users.notes.store', ['user' => $user]),
                ['bb_text' => $bb_text])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.notes.index', $user));

        $note = $user->notes()->first();

        $this->assertEquals('<strong class="bb">test</strong>', $note->text);
        $this->assertTrue($note->external_images_downloaded);

        $this->actingAs($user)
            ->get(route('users.notes.index', ['user' => $user]))
            ->assertOk()
            ->assertSeeText('test');
    }

    public function testEditHttp()
    {
        $note = UserNote::factory()->create();

        $this->actingAs($note->create_user)
            ->get(route('notes.edit', ['note' => $note->id]))
            ->assertOk()
            ->assertSeeText($note->bb_text);
    }

    public function testEditHttpUserNotFound()
    {
        $admin = User::factory()->create();

        $note = UserNote::factory()->create();

        $note->create_user->delete();

        $this->actingAs($admin)
            ->get(route('notes.edit', ['note' => $note->id]))
            ->assertForbidden();
    }

    public function testEditHttpOtherUser()
    {
        $note = UserNote::factory()->create();

        $other_user = User::factory()->create();

        $this->actingAs($other_user)
            ->get(route('notes.edit', ['note' => $note->id]))
            ->assertForbidden();
    }

    public function testUpdateHttp()
    {
        $note = UserNote::factory()->create(['external_images_downloaded' => false]);

        $bb_text = $this->faker->realText(100);

        $this->actingAs($note->create_user)
            ->patch(route('notes.update', ['note' => $note->id]),
                ['bb_text' => $bb_text])
            ->assertRedirect(route('users.notes.index', $note->create_user));

        $this->get(route('users.notes.index', ['user' => $note->create_user]))
            ->assertOk()
            ->assertSeeText($bb_text);

        $note->refresh();

        $this->assertTrue($note->external_images_downloaded);
    }

    public function testUpdateHttpOtherUser()
    {
        $note = UserNote::factory()->create();

        $other_user = User::factory()->create();

        $this->actingAs($other_user)
            ->patch(route('notes.update', ['note' => $note->id]),
                ['bb_text' => 'text'])
            ->assertForbidden();
    }

    public function testDelete()
    {
        $note = UserNote::factory()->create();

        $this->actingAs($note->create_user)
            ->delete(route('notes.destroy', ['id' => $note->id]))
            ->assertOk();

        $note->refresh();
        $this->assertTrue($note->trashed());

        $this->actingAs($note->create_user)
            ->delete(route('notes.destroy', ['id' => $note->id]))
            ->assertOk();

        $note->refresh();
        $this->assertFalse($note->trashed());
    }

    public function testDelteHttpOtherUser()
    {
        $note = UserNote::factory()->create();

        $other_user = User::factory()->create();

        $this->actingAs($other_user)
            ->delete(route('notes.destroy', ['id' => $note->id]))
            ->assertForbidden();
    }

    public function testNotFound()
    {
        $note = UserNote::factory()->create();

        $note->delete();

        $this->actingAs($note->create_user)
            ->get(route('notes.show', ['note' => $note->id]))
            ->assertNotFound();
    }

    public function testBBEmpty()
    {
        $note = UserNote::factory()->create();

        $this->expectException(QueryException::class);

        $note->bb_text = '';
        $note->save();
    }
}
