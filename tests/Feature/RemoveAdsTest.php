<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;

class RemoveAdsTest extends TestCase
{
    public function testPageIsOkWhenAuth()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('remove_ads'))
            ->assertOk();
    }

    public function testPageIsOkWhenGuest()
    {
        $this->get(route('remove_ads'))
            ->assertOk();
    }
}
