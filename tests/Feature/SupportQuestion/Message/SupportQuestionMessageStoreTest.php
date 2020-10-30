<?php

namespace Tests\Feature\SupportQuestion\Message;

use App\Jobs\SupportQuestion\UpdateNumberInProgressQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfAnsweredQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfNewQuestions;
use App\Jobs\User\UpdateUserNumberInProgressQuestions;
use App\Notifications\NewSupportResponseNotification;
use App\SupportQuestion;
use App\SupportQuestionMessage;
use App\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class SupportQuestionMessageStoreTest extends TestCase
{
	public function testStoreNewRequest()
	{
		$messageNew = factory(SupportQuestionMessage::class)
			->make();

		$user = factory(User::class)
			->create();

		$response = $this->actingAs($user)
			->post(route('support_question_messages.store'),
				['bb_text' => $messageNew->bb_text])
			->assertSessionHasNoErrors()
			->assertRedirect()
			->assertSessionHas('success', __('The message has been successfully sent'));

		$message = $user->createdSupportMessages()->first();

		$this->assertNotNull($message);

		$supportQuestion = $message->supportQuestion;

		$response->assertRedirect(route('support_questions.show', ['support_question' => $supportQuestion->id]));

		$this->assertEquals($supportQuestion->id, $message->support_question_id);
		$this->assertEquals($user->id, $message->create_user_id);
		$this->assertEquals($messageNew->text, $message->text);

		$this->assertEquals(1, $supportQuestion->number_of_messages);
		$this->assertEquals($message->id, $supportQuestion->latest_message_id);
	}

	public function testStoreInExistedRequest()
	{
		Bus::fake();
		Notification::fake();

		$supportQuestion = factory(SupportQuestion::class)
			->states('sent_for_review')
			->create();

		$messageNew = factory(SupportQuestionMessage::class)
			->make();

		$user = $supportQuestion->create_user;
		$supportQuestion->status_changed_user_id = $user->id;
		$supportQuestion->save();

		$this->actingAs($user)
			->post(route('support_question_messages.store', ['support_question' => $supportQuestion->id]),
				['bb_text' => $messageNew->bb_text])
			->assertSessionHasNoErrors()
			->assertRedirect(route('support_questions.show', ['support_question' => $supportQuestion->id]))
			->assertSessionHas('success', __('The message has been successfully sent'));

		$supportQuestion->refresh();

		$message = $supportQuestion->messages()->orderBy('id', 'desc')->first();

		$this->assertEquals($supportQuestion->id, $message->support_question_id);
		$this->assertEquals($user->id, $message->create_user_id);
		$this->assertEquals($messageNew->text, $message->text);

		$this->assertEquals(1, $supportQuestion->number_of_messages);
		$this->assertEquals($message->id, $supportQuestion->latest_message_id);

		Bus::assertNotDispatched(UpdateNumberInProgressQuestions::class);
		Bus::assertNotDispatched(UpdateNumberOfAnsweredQuestions::class);
		Bus::assertNotDispatched(UpdateNumberOfNewQuestions::class);

		Bus::assertDispatched(UpdateUserNumberInProgressQuestions::class, function ($job) use ($supportQuestion) {
			return $job->user->is($supportQuestion->status_changed_user);
		});

		Notification::assertNotSentTo($user, NewSupportResponseNotification::class);
	}

	public function testStoreSupportResponse()
	{
		Bus::fake();
		Notification::fake();

		$supportQuestion = factory(SupportQuestion::class)
			->states('with_message', 'review_starts')
			->create();

		$messageNew = factory(SupportQuestionMessage::class)
			->make();

		$supportUser = $supportQuestion->status_changed_user;

		$this->actingAs($supportUser)
			->post(route('support_question_messages.store', ['support_question' => $supportQuestion->id]),
				['bb_text' => $messageNew->bb_text])
			->assertSessionHasNoErrors()
			->assertRedirect(route('support_questions.show', ['support_question' => $supportQuestion->id]));

		$supportQuestion->refresh();

		$message = $supportQuestion->messages()->orderBy('id', 'desc')->first();

		Notification::assertNotSentTo($supportUser, NewSupportResponseNotification::class);
		Notification::assertSentTo($supportQuestion->create_user, NewSupportResponseNotification::class);
	}
}
