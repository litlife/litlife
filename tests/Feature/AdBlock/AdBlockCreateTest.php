<?php

namespace Tests\Feature\AdBlock;

use App\AdBlock;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class AdBlockCreateTest extends TestCase
{
    public function testCreate()
    {
        $user = User::factory()->create();
        $user->group->manage_ad_blocks = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('ad_blocks.create'))
            ->assertOk();
    }

    public function testStore()
    {
        $user = User::factory()->create();
        $user->group->manage_ad_blocks = true;
        $user->push();

        $blockNew = AdBlock::factory()
            ->make();

        $this->actingAs($user)
            ->post(route('ad_blocks.store', [
                'name' => $blockNew->name,
                'code' => $blockNew->code,
                'description' => $blockNew->description
            ]))->assertSessionHasNoErrors()
            ->assertRedirect(route('ad_blocks.index'))
            ->assertSessionHas('success', __('Ad block created successfully'));

        $block = AdBlock::name($blockNew->name)->first();

        $this->assertEquals($blockNew->name, $block->name);
        $this->assertEquals($blockNew->code, $block->code);
        $this->assertEquals($blockNew->description, $block->description);
        $this->assertFalse($block->isEnabled());
        $this->assertNotNull($block->user_updated_at);
    }

    public function testStoreUniqueName()
    {
        $user = User::factory()->create();
        $user->group->manage_ad_blocks = true;
        $user->push();

        $block = AdBlock::factory()->enabled()->create();

        Carbon::setTestNow(now()->addMinute());

        $this->actingAs($user)
            ->post(route('ad_blocks.store', [
                'name' => $block->name,
                'code' => $block->code,
                'description' => $block->description
            ]))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertEquals(2, AdBlock::where('name', $block->name)->count());

        $block->refresh();

        $this->assertTrue($block->isEnabled());

        $block2 = AdBlock::where('name', $block->name)
            ->latest()
            ->first();

        $this->assertFalse($block2->isEnabled());
    }

    public function testCodeAsViewError()
    {
        $user = User::factory()->create();
        $user->group->manage_ad_blocks = true;
        $user->push();

        $blockNew = AdBlock::factory()
            ->make();

        $this->actingAs($user)
            ->post(route('ad_blocks.store', [
                'name' => $blockNew->name,
                'code' => 'test',
                'description' => $blockNew->description
            ]))
            ->assertRedirect()
            ->assertSessionHasErrors(['code' => __('The code cannot be the same as the path to the view file')]);
    }
}
