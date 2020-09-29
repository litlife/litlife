<?php

namespace Tests\Feature\AdBlock;

use App\AdBlock;
use App\User;
use Tests\TestCase;

class AdBlockCreateTest extends TestCase
{
	public function testCreate()
	{
		$user = factory(User::class)->create();
		$user->group->manage_ad_blocks = true;
		$user->push();

		$this->actingAs($user)
			->get(route('ad_blocks.create'))
			->assertOk();
	}

	public function testStore()
	{
		$user = factory(User::class)->create();
		$user->group->manage_ad_blocks = true;
		$user->push();

		$blockNew = factory(AdBlock::class)
			->make();

		$this->actingAs($user)
			->post(route('ad_blocks.store', [
				'name' => $blockNew->name,
				'code' => $blockNew->code
			]))->assertSessionHasNoErrors()
			->assertRedirect(route('ad_blocks.index'))
			->assertSessionHas('success', __('Ad block created successfully'));

		$block = AdBlock::name($blockNew->name)->first();

		$this->assertEquals($blockNew->name, $block->name);
		$this->assertEquals($blockNew->code, $block->code);
	}

	public function testStoreUniqueName()
	{
		$user = factory(User::class)->create();
		$user->group->manage_ad_blocks = true;
		$user->push();

		$block = factory(AdBlock::class)
			->create();

		$this->actingAs($user)
			->post(route('ad_blocks.store', [
				'name' => $block->name,
				'code' => $block->code
			]))
			->assertSessionHasErrors('name')
			->assertRedirect();
	}
}
