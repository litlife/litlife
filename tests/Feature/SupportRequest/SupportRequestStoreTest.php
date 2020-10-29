<?php

namespace Tests\Feature\SupportRequest;

use App\Events\NumberOfUnsolvedSupportRequestsHasChanged;
use App\SupportRequest;
use App\SupportRequestMessage;
use App\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use Tests\TestCase;

class SupportRequestStoreTest extends TestCase
{
	public function testIsOk()
	{
		Event::fake(NumberOfUnsolvedSupportRequestsHasChanged::class);

		$user = factory(User::class)
			->create();

		$supportRequestNew = factory(SupportRequest::class)
			->make();

		$supportRequestMessageNew = factory(SupportRequestMessage::class)
			->make();

		$response = $this->actingAs($user)
			->post(route('support_requests.store', ['user' => $user]),
				array_merge($supportRequestNew->toArray(), $supportRequestMessageNew->toArray()))
			->assertSessionHasNoErrors()
			->assertRedirect()
			->assertSessionHas('success', __('Question to support has been sent successfully'));

		$message = $user->createdSupportMessages()->first();

		$this->assertNotNull($message);

		$supportRequest = $message->supportRequest;

		$this->assertEquals($supportRequestNew->title, $supportRequest->title);

		$response->assertRedirect(route('support_requests.show', ['support_request' => $supportRequest->id]));

		$this->assertEquals($supportRequest->id, $message->support_request_id);
		$this->assertEquals($user->id, $message->create_user_id);
		$this->assertEquals($supportRequestMessageNew->text, $message->text);

		$this->assertEquals(1, $supportRequest->number_of_messages);
		$this->assertEquals($message->id, $supportRequest->latest_message_id);

		Event::assertDispatched(NumberOfUnsolvedSupportRequestsHasChanged::class);
	}

	public function testWithoutTitle()
	{
		$user = factory(User::class)
			->create();

		$supportRequestMessageNew = factory(SupportRequestMessage::class)
			->make();

		$response = $this->actingAs($user)
			->post(route('support_requests.store', ['user' => $user]), $supportRequestMessageNew->toArray())
			->assertSessionHasNoErrors()
			->assertRedirect()
			->assertSessionHas('success', __('Question to support has been sent successfully'));

		$message = $user->createdSupportMessages()->first();

		$this->assertNotNull($message);

		$supportRequest = $message->supportRequest;

		$this->assertEquals(Str::limit($supportRequestMessageNew->text, 100), $supportRequest->title);

		$response->assertRedirect(route('support_requests.show', ['support_request' => $supportRequest->id]));

		$this->assertEquals($supportRequest->id, $message->support_request_id);
		$this->assertEquals($user->id, $message->create_user_id);
		$this->assertEquals($supportRequestMessageNew->text, $message->text);

		$this->assertEquals(1, $supportRequest->number_of_messages);
		$this->assertEquals($message->id, $supportRequest->latest_message_id);
	}
}
