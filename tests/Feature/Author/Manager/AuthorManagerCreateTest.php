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
        $admin = User::factory()->create();
        $admin->group->moderator_add_remove = true;
        $admin->push();

        $author = Author::factory()->create();

        $user = User::factory()->create();

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
        $admin = User::factory()->create();
        $admin->group->moderator_add_remove = true;
        $admin->push();

        $author = Author::factory()->create();

        $user = User::factory()->create();

        $manager = Manager::factory()->create([
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
        $admin = User::factory()->admin()->create();

        $author = Author::factory()->create();

        $user = User::factory()->create();

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

    public function testCanIfTheRejectedRequestExists()
    {
        $admin = User::factory()->create();
        $admin->group->moderator_add_remove = true;
        $admin->push();

        $manager = Manager::factory()
            ->rejected()
            ->create();

        $user = $manager->user;
        $author = $manager->manageable;

        $response = $this->actingAs($admin)
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
}
