<?php

namespace Tests\Feature\SupportRequest\Message;

use App\Events\NumberOfUnsolvedSupportRequestsHasChanged;
use App\SupportRequest;
use App\SupportRequestMessage;
use App\User;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class SupportRequestMessageStoreTest extends TestCase
{
	public function testStoreNewRequest()
	{
		Event::fake(NumberOfUnsolvedSupportRequestsHasChanged::class);

		$messageNew = factory(SupportRequestMessage::class)
			->make();

		$user = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->post(route('support_request_messages.store'),
				['text' => $messageNew->text])
			->assertSessionHasNoErrors()
			->assertRedirect()
			->assertSessionHas('success', __('The message has been successfully sent'));

		$message = $user->createdSupportMessages()->first();

		$this->assertNotNull($message);

		$supportRequest = $message->supportRequest;

		$response->assertRedirect(route('support_requests.show', ['support_request' => $supportRequest->id]));

		$this->assertEquals($supportRequest->id, $message->support_request_id);
		$this->assertEquals($user->id, $message->create_user_id);
		$this->assertEquals($messageNew->text, $message->text);

		$this->assertEquals(1, $supportRequest->number_of_messages);
		$this->assertEquals($message->id, $supportRequest->latest_message_id);

		Event::assertDispatched(NumberOfUnsolvedSupportRequestsHasChanged::class);
	}

	public function testStoreInExistedRequest()
	{
		$supportRequest = factory(SupportRequest::class)
			->create();

		$messageNew = factory(SupportRequestMessage::class)
			->make();

		$user = $supportRequest->create_user;

		$this->actingAs($user)
			->post(route('support_request_messages.store', ['support_request' => $supportRequest->id]),
				['text' => $messageNew->text])
			->assertSessionHasNoErrors()
			->assertRedirect(route('support_requests.show', ['support_request' => $supportRequest->id]))
			->assertSessionHas('success', __('The message has been successfully sent'));

		$supportRequest->refresh();

		$message = $supportRequest->messages()->orderBy('id', 'desc')->first();

		$this->assertEquals($supportRequest->id, $message->support_request_id);
		$this->assertEquals($user->id, $message->create_user_id);
		$this->assertEquals($messageNew->text, $message->text);

		$this->assertEquals(1, $supportRequest->number_of_messages);
		$this->assertEquals($message->id, $supportRequest->latest_message_id);
	}
}
