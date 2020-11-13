<?php

namespace Tests\Feature\Sequence;

use App\Sequence;
use App\User;
use App\UserSequence;
use Tests\TestCase;

class SequenceFavoriteToggleTest extends TestCase
{
    public function testToggle()
    {
        $sequence = Sequence::factory()->create();

        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('sequences.favorites.toggle', ['sequence' => $sequence]))
            ->assertOk()
            ->assertJson([
                'result' => 'attached',
                'added_to_favorites_count' => 1
            ]);

        $user->refresh();
        $sequence->refresh();

        $this->assertTrue($user->is($sequence->addedToFavoritesUsers()->first()));
        $this->assertEquals(1, $sequence->added_to_favorites_count);
        $this->assertTrue($sequence->is($user->sequences()->first()));

        $this->actingAs($user)
            ->get(route('sequences.favorites.toggle', ['sequence' => $sequence]))
            ->assertOk()
            ->assertJson([
                'result' => 'detached',
                'added_to_favorites_count' => 0
            ]);

        $sequence->refresh();

        $this->assertEquals(0, $sequence->added_to_favorites_count);
    }

    public function testToggleIfAuthorDeleted()
    {
        $user_sequence = UserSequence::factory()->create();

        $sequence = $user_sequence->sequence;
        $user = $user_sequence->user;

        $sequence->delete();

        $this->actingAs($user)
            ->get(route('sequences.favorites.toggle', ['sequence' => $sequence]))
            ->assertOk()
            ->assertJson([
                'result' => 'detached',
                'added_to_favorites_count' => 0
            ]);

        $sequence->refresh();

        $this->assertEquals(0, $sequence->added_to_favorites_count);
    }
}
