<?php

namespace Tests\Feature\AdBlock;

use App\AdBlock;
use App\View\Components\AdBlock as Component;
use Tests\TestCase;

class AdBlockComponentTest extends TestCase
{
	public function testIfExists()
	{
		$block = AdBlock::factory()->enabled()->create();

		$component = new Component($block->name);

		$this->assertEquals('<script type="text/javascript">alert("test");</script>',
			$component->render());
	}

	public function testIfNotExists()
	{
		$block = factory(AdBlock::class)
			->states('enabled')
			->make();

		$component = new Component($block->name);

		$this->assertEquals('',
			$component->render());
	}
}
