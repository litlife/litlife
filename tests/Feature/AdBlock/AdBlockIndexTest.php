<?php

namespace Tests\Feature\AdBlock;

use App\AdBlock;
use App\User;
use Tests\TestCase;

class AdBlockIndexTest extends TestCase
{
    public function testNothingFound()
    {
        $user = User::factory()->create();
        $user->group->manage_ad_blocks = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('ad_blocks.index'))
            ->assertOk();
    }

    public function testFound()
    {
        $user = User::factory()->create();
        $user->group->manage_ad_blocks = true;
        $user->push();

        $block = AdBlock::factory()->create();

        $this->actingAs($user)
            ->get(route('ad_blocks.index'))
            ->assertOk()
            ->assertDontSeeText(__('No blocks found'));
    }
}
