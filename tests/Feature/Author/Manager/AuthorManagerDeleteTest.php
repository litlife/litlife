<?php

namespace Tests\Feature\AuthorManager;

use App\AuthorSaleRequest;
use App\Manager;
use App\User;
use Tests\TestCase;

class AuthorManagerDeleteTest extends TestCase
{
    public function testDetachAuthorUserGroupOnManagerDelete()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()
            ->character_author()
            ->accepted()
            ->create();

        $user = $manager->user;
        $user->attachUserGroupByNameIfExists('Автор');

        $this->assertEquals('Автор', $user->groups()->disableCache()->whereName('Автор')->first()->name);
        $this->assertEquals(2, $user->groups()->disableCache()->count());

        $this->actingAs($admin)
            ->get(route('managers.destroy', ['manager' => $manager->id]))
            ->assertRedirect();

        $manager->refresh();
        $user->refresh();

        $this->assertSoftDeleted($manager);
        $this->assertNull($user->groups()->disableCache()->whereName('Автор')->first());
        $this->assertEquals(1, $user->groups()->disableCache()->count());
    }

    public function testRemoveSaleRequestIfManagerDestroyed()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()
            ->character_author()
            ->accepted()
            ->create();

        $user = $manager->user;
        $author = $manager->manageable;

        $saleRequest = AuthorSaleRequest::factory()
            ->sent_for_review()
            ->create([
                'create_user_id' => $user,
                'manager_id' => $manager->id,
                'author_id' => $author->id
            ]);

        $this->actingAs($admin)
            ->get(route('managers.destroy', ['manager' => $manager->id]))
            ->assertRedirect();

        $manager->refresh();
        $user->refresh();
        $saleRequest->refresh();

        $this->assertSoftDeleted($manager);
        $this->assertSoftDeleted($saleRequest);
    }
}
