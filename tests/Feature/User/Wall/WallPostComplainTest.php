<?php

namespace Tests\Feature\User\Wall;

use App\Blog;
use App\User;
use Tests\TestCase;

class WallPostComplainTest extends TestCase
{
	public function testCanComplain()
	{
		$user = factory(User::class)->create();
		$user->group->complain = true;
		$user->push();
		$user->refresh();

		$blog = factory(Blog::class)
			->create();

		$this->assertTrue($user->can('complain', $blog));
	}
}
