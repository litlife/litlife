<?php

namespace Tests\Feature\AdBlock;

use App\AdBlock;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdBlockEnableTest extends TestCase
{
	public function testEnable()
	{
		$user = factory(User::class)->create();
		$user->group->manage_ad_blocks = true;
		$user->push();

		$name = Str::random(8);

		$block1 = factory(AdBlock::class)
			->states('enabled')
			->create(['name' => $name]);

		$block2 = factory(AdBlock::class)
			->create(['name' => $name]);

		$this->assertTrue($block1->isEnabled());
		$this->assertFalse($block2->isEnabled());

		$this->actingAs($user)
			->get(route('ad_blocks.enable', ['ad_block' => $block2->id]))
			->assertRedirect(route('ad_blocks.index'))
			->assertSessionHas('success', __('Ad block :name enabled', ['name' => $name]));

		$block1->refresh();
		$block2->refresh();

		$this->assertFalse($block1->isEnabled());
		$this->assertTrue($block2->isEnabled());
	}
}