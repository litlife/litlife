<?php

namespace Tests\Feature\Forum\ForumGroup;

use App\Enums\VariablesEnum;
use App\ForumGroup;
use App\Variable;
use Tests\TestCase;

class ForumGroupTest extends TestCase
{
	public function testEmptyGetSort()
	{
		Variable::where('name', VariablesEnum::getValue('ForumGroupSort'))
			->delete();

		$forumGroup = factory(ForumGroup::class)
			->create();

		$this->assertNull($forumGroup->getSort());
	}
}
