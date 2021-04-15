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

        $admin = User::factory()->admin()->create();

        $author = Author::factory()->create();

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

        $admin = User::factory()->admin()->create();

        $author = Author::factory()->create();
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
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->character_author()->accepted()->create();

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

    public function testDeleteIfMultipleRequestsForVerificationHaveBeenSent()
    {
        $author = Author::factory()
            ->private()
            ->create();

        $user = $author->create_user;

        $manager = Manager::factory()
            ->character_author()
            ->private()
            ->create([
                'user_id' => $user->id,
                'create_user_id' => $user->id,
                'manageable_id' => $author->id,
                'manageable_type' => 'author'
            ]);

        $manager2 = Manager::factory()
            ->character_author()
            ->private()
            ->create([
                'user_id' => $user->id,
                'create_user_id' => $user->id,
                'manageable_id' => $author->id,
                'manageable_type' => 'author'
            ]);

        $this->assertEquals(2, $author->managers()->count());

        $this->actingAs($user)
            ->get(route('authors.delete', ['author' => $author]))
            ->assertRedirect();

        $manager->refresh();
        $manager2->refresh();

        $this->assertTrue($manager->trashed());
        $this->assertTrue($manager2->trashed());
    }
}
