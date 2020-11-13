<?php

namespace Tests\Feature\User\Wall;

use App\Blog;
use App\User;
use Tests\TestCase;

class WallPostComplainTest extends TestCase
{
    public function testCanComplain()
    {
        $user = User::factory()->create();
        $user->group->complain = true;
        $user->push();
        $user->refresh();

        $blog = Blog::factory()->create();

        $this->assertTrue($user->can('complain', $blog));
    }
}
