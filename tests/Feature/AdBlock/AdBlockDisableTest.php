<?php

namespace Tests\Feature\AdBlock;

use App\AdBlock;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdBlockDisableTest extends TestCase
{
	public function testDisable()
	{
		$user = factory(User::class)->create();
		$user->group->manage_ad_blocks = true;
		$user->push();

		$name = Str::random(8);

		$block = factory(AdBlock::class)
			->states('enabled')
			->create(['name' => $name]);

		$this->assertTrue($block->isEnabled());

		$this->actingAs($user)
			->get(route('ad_blocks.disable', ['ad_block' => $block->id]))
			->assertRedirect(route('ad_blocks.index'))
			->assertSessionHas('success', __('Ad block :name disabled', ['name' => $name]));

		$block->refresh();

		$this->assertFalse($block->isEnabled());
	}
}
