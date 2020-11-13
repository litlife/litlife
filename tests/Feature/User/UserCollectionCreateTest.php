<?php

namespace Tests\Feature\User;

use App\Collection;
use Tests\TestCase;

class UserCollectionCreateTest extends TestCase
{
    public function testUserCreatedCollectionsHttp()
    {
        $collection = Collection::factory()->create(['title' => uniqid()]);

        $user = $collection->create_user;

        $this->actingAs($user)
            ->get(route('users.collections.created', ['user' => $user]))
            ->assertOk()
            ->assertSeeText($collection->title);
    }
}
