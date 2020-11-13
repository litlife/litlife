<?php

namespace Tests\Feature\AdBlock;

use App\AdBlock;
use App\User;
use Tests\TestCase;

class AdBlockEditTest extends TestCase
{
    public function testEdit()
    {
        $user = User::factory()->create();
        $user->group->manage_ad_blocks = true;
        $user->push();

        $block = AdBlock::factory()->create();

        $this->actingAs($user)
            ->get(route('ad_blocks.edit', ['ad_block' => $block->id]))
            ->assertOk();
    }

    public function testEditIfGuest()
    {
        $block = AdBlock::factory()->create();

        $this->get(route('ad_blocks.edit', ['ad_block' => $block->id]))
            ->assertStatus(401);
    }

    public function testUpdate()
    {
        $user = User::factory()->create();
        $user->group->manage_ad_blocks = true;
        $user->push();

        $block = AdBlock::factory()->create();

        $blockNew = AdBlock::factory()
            ->make();

        $this->actingAs($user)
            ->patch(route('ad_blocks.update', ['ad_block' => $block->id]), [
                'name' => $blockNew->name,
                'code' => $blockNew->code,
                'description' => $blockNew->description
            ])->assertSessionHasNoErrors()
            ->assertRedirect(route('ad_blocks.index'))
            ->assertSessionHas('success', __('The data was successfully stored'));

        $block->refresh();

        $this->assertEquals($blockNew->name, $block->name);
        $this->assertEquals($blockNew->code, $block->code);
        $this->assertEquals($blockNew->description, $block->description);
        $this->assertNotNull($block->user_updated_at);
    }
}
