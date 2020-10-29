<?php

namespace Tests\Feature\SupportRequest;

use App\Events\NumberOfUnsolvedSupportRequestsHasChanged;
use App\SupportRequest;
use App\SupportRequestMessage;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SupportRequestMarkAsSolvedTest extends TestCase
{
	public function testWithoutAjax()
	{
		Event::fake(NumberOfUnsolvedSupportRequestsHasChanged::class);

		$supportRequest = factory(SupportRequest::class)
			->states('review_starts')
			->create();

		$user = $supportRequest->status_changed_user;
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->actingAs($user)
			->get(route('support_requests.solve', $supportRequest))
			->assertSessionHasNoErrors()
			->assertRedirect(route('support_requests.unsolved'))
			->assertSessionHas('success', __('Thank you! You marked the support request as resolved'));

		$supportRequest->refresh();

		$this->assertTrue($supportRequest->isAccepted());

		Event::assertDispatched(NumberOfUnsolvedSupportRequestsHasChanged::class);
	}

	public function testWithAjax()
	{
		Event::fake(NumberOfUnsolvedSupportRequestsHasChanged::class);

		$supportRequest = factory(SupportRequest::class)
			->states('review_starts')
			->create();

		$user = $supportRequest->status_changed_user;
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->actingAs($user)
			->ajax()
			->get(route('support_requests.solve', $supportRequest))
			->assertSessionHasNoErrors()
			->assertViewIs('support_request.status')
			->assertViewHas('item', $supportRequest);

		$supportRequest->refresh();

		$this->assertTrue($supportRequest->isAccepted());

		Event::assertDispatched(NumberOfUnsolvedSupportRequestsHasChanged::class);
	}

	public function testIfAuthUserCreator()
	{
		$supportRequest = factory(SupportRequest::class)
			->states('review_starts', 'with_message')
			->create();

		$user = $supportRequest->create_user;

		$message = factory(SupportRequestMessage::class)
			->make();

		$supportRequest->messages()->save($message);
		$supportRequest->latest_message_id = $message->id;
		$supportRequest->save();

		$this->actingAs($user)
			->get(route('support_requests.solve', $supportRequest))
			->assertSessionHasNoErrors()
			->assertRedirect(route('users.support_requests.index', ['user' => $user]))
			->assertSessionHas('success', __('Thank you! You marked the support request as resolved'));

		$supportRequest->refresh();

		$this->assertTrue($supportRequest->isAccepted());
	}
}
