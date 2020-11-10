<?php

namespace Tests\Feature\Complain;

use App\Complain;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class ComplainShowTest extends TestCase
{
	public function testShowIsOkIfOnReview()
	{
		$user = User::factory()->create();

		$complain = Complain::factory()->comment()->sent_for_review()->create();

		$user = $complain->create_user;
		$user->group->complain = true;
		$user->push();

		$this->actingAs($user)
			->get(route('complaints.show', $complain->id))
			->assertOk()
			->assertSeeText(__('complain.complaint_is_pending'));
	}

	public function testComplainShowStringAsID()
	{
		$admin = User::factory()->admin()->create();

		$complain = Complain::factory()->wall_post()->create();

		$this->actingAs($admin)
			->get(route('complaints.show', ['complain' => Str::random(5)]))
			->assertNotFound();
	}

	public function testSeeReviewed()
	{
		$complain = Complain::factory()->comment()->accepted()->create();

		$user = $complain->create_user;
		$user->group->complain = true;
		$user->push();

		$this->actingAs($user)
			->get(route('complaints.show', ['complain' => $complain]))
			->assertOk()
			->assertSeeText(__('Сomplaint has been reviewed'));
	}
}
