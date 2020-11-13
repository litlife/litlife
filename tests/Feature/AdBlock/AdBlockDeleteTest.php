<?php

namespace Tests\Feature\AdBlock;

use App\AdBlock;
use App\User;
use Tests\TestCase;

class AdBlockDeleteTest extends TestCase
{
    public function testDestroy()
    {
        $user = User::factory()->create();
        $user->group->manage_ad_blocks = true;
        $user->push();

        $block = AdBlock::factory()->create();

        $this->actingAs($user)
            ->delete(route('ad_blocks.destroy', ['ad_block' => $block->id]))
            ->assertRedirect(route('ad_blocks.index'))
            ->assertSessionHas('success', __('Ad block was successfully deleted'));

        if (method_exists($block, 'trashed')) {
            $block->refresh();
            $this->assertTrue($block->trashed());
        } else {
            $this->assertDatabaseMissing('ad_blocks', ['id' => $block->id]);
        }
    }

    public function testDelete()
    {
        $user = User::factory()->create();
        $user->group->manage_ad_blocks = true;
        $user->push();

        $block = AdBlock::factory()->create();

        $this->actingAs($user)
            ->get(route('ad_blocks.delete', ['ad_block' => $block->id]))
            ->assertRedirect(route('ad_blocks.index'))
            ->assertSessionHas('success', __('Ad block was successfully deleted'));

        if (method_exists($block, 'trashed')) {
            $block->refresh();
            $this->assertTrue($block->trashed());
        } else {
            $this->assertDatabaseMissing('ad_blocks', ['id' => $block->id]);
        }
    }
}
