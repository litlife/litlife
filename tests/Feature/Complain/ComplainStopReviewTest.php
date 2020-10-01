<?php

namespace Tests\Feature\Complain;

use App\Complain;
use App\User;
use Tests\TestCase;

class ComplainStopReviewTest extends TestCase
{
	public function testStopReviewHttp()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$complain = factory(Complain::class)->states('comment', 'review_starts')->create();
		$complain->status_changed_user_id = $admin->id;
		$complain->save();

		$count = Complain::getCachedOnModerationCount();

		$this->actingAs($admin)
			->get(route('complains.stop_review', $complain))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$complain->refresh();

		$this->assertEquals($count, Complain::getCachedOnModerationCount());
		$this->assertTrue($complain->isSentForReview());
	}
}
