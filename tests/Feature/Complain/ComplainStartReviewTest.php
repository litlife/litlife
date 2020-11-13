<?php

namespace Tests\Feature\Complain;

use App\Complain;
use App\User;
use Tests\TestCase;

class ComplainStartReviewTest extends TestCase
{
    public function testStartReviewHttp()
    {
        $admin = User::factory()->admin()->create();

        $complain = Complain::factory()->comment()->sent_for_review()->create();

        $count = Complain::getCachedOnModerationCount();

        $this->actingAs($admin)
            ->get(route('complains.start_review', $complain))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $complain->refresh();

        $this->assertEquals($count, Complain::getCachedOnModerationCount());
        $this->assertTrue($complain->isReviewStarts());
    }
}
