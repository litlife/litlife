<?php

namespace Tests\Feature\User\Sequence;

use App\UserSequence;
use Tests\TestCase;

class UserSequenceFavoriteTest extends TestCase
{
    public function test()
    {
        $userSequence = UserSequence::factory()
            ->create();

        $user = $userSequence->user;

        $this->actingAs($user)
            ->get(route('users.sequences', ['user' => $user]))
            ->assertOk();
    }
}
