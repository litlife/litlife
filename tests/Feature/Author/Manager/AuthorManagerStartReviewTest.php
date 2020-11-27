<?php

namespace Tests\Feature\Author\Manager;

use App\Manager;
use App\User;
use Tests\TestCase;

class AuthorManagerStartReviewTest extends TestCase
{
    public function testStartReview()
    {
        $admin = User::factory()
            ->admin()
            ->create();

        $manager = Manager::factory()
            ->sent_for_review()
            ->create();

        $this->actingAs($admin)
            ->get(route('managers.start_review', $manager))
            ->assertRedirect();

        $manager->refresh();

        $this->assertTrue($manager->isReviewStarts());
    }
}
