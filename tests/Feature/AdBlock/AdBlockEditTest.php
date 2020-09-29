<?php

namespace Tests\Feature\AdBlock;

use App\AdBlock;
use App\User;
use Tests\TestCase;

class AdBlockEditTest extends TestCase
{
	public function testEdit()
	{
		$user = factory(User::class)->create();
		$user->group->manage_ad_blocks = true;
		$user->push();

		$block = factory(AdBlock::class)
			->create();

		$this->actingAs($user)
			->get(route('ad_blocks.edit', ['ad_block' => $block->id]))
			->assertOk();
	}

	public function testUpdate()
	{
		$user = factory(User::class)->create();
		$user->group->manage_ad_blocks = true;
		$user->push();

		$block = factory(AdBlock::class)
			->create();

		$blockNew = factory(AdBlock::class)
			->make();

		$this->actingAs($user)
			->patch(route('ad_blocks.update', ['ad_block' => $block->id]), [
				'name' => $blockNew->name,
				'code' => $blockNew->code
			])->assertSessionHasNoErrors()
			->assertRedirect(route('ad_blocks.index'))
			->assertSessionHas('success', __('The data was successfully stored'));

		$block->refresh();

		$this->assertEquals($blockNew->name, $block->name);
		$this->assertEquals($blockNew->code, $block->code);
	}
}
