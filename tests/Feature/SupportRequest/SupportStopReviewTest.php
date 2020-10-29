<?php

namespace Tests\Feature\SupportRequest;

use App\SupportRequest;
use App\User;
use Tests\TestCase;

class SupportStopReviewTest extends TestCase
{
	public function testWithoutAjax()
	{
		$supportRequest = factory(SupportRequest::class)
			->states('review_starts')
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$supportRequest->status_changed_user_id = $user->id;
		$supportRequest->push();
		$supportRequest->refresh();

		$this->actingAs($user)
			->get(route('support_requests.stop_review', $supportRequest))
			->assertSessionHasNoErrors()
			->assertRedirect(route('support_requests.unsolved'))
			->assertSessionHas('success', __('You refused to resolve the request'));

		$supportRequest->refresh();

		$this->assertTrue($supportRequest->isSentForReview());
	}

	public function testWithAjax()
	{
		$supportRequest = factory(SupportRequest::class)
			->states('review_starts')
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$supportRequest->status_changed_user_id = $user->id;
		$supportRequest->push();
		$supportRequest->refresh();

		$this->actingAs($user)
			->ajax()
			->get(route('support_requests.stop_review', $supportRequest))
			->assertSessionHasNoErrors()
			->assertViewIs('support_request.status')
			->assertViewHas('item', $supportRequest);

		$supportRequest->refresh();

		$this->assertTrue($supportRequest->isSentForReview());
	}
}
