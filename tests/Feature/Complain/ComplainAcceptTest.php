<?php

namespace Tests\Feature\Complain;

use App\Complain;
use App\User;
use Tests\TestCase;

class ComplainAcceptTest extends TestCase
{
	public function testAcceptHttp()
	{
		$admin = User::factory()->admin()->create();

		$complain = Complain::factory()->comment()->review_starts()->create();
		$complain->status_changed_user_id = $admin->id;
		$complain->save();

		$count = Complain::getCachedOnModerationCount();

		$this->actingAs($admin)
			->get(route('complains.approve', $complain))
			->assertSessionHasNoErrors()
			->assertRedirect();

		$complain->refresh();

		$this->assertEquals(($count - 1), Complain::getCachedOnModerationCount());
		$this->assertTrue($complain->isAccepted());
	}
}
