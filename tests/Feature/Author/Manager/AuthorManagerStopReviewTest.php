<?php

namespace Tests\Feature\Author\Manager;

use App\Manager;
use App\User;
use Tests\TestCase;

class AuthorManagerStopReviewTest extends TestCase
{
    public function testStopReview()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->review_starts()->create();
        $manager->status_changed_user_id = $admin->id;
        $manager->save();
        $manager->refresh();

        $this->assertNotNull($manager->status_changed_user_id);

        $this->assertTrue($manager->isReviewStarts());

        $this->actingAs($admin)
            ->get(route('managers.stop_review', $manager))
            ->assertRedirect();

        $manager->refresh();

        $this->assertTrue($manager->isSentForReview());
    }
}
