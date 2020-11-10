<?php

namespace Tests\Feature\SupportQuestion;

use App\Events\NumberOfUnsolvedSupportQuestionsHasChanged;
use App\Jobs\SupportQuestion\UpdateNumberInProgressQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfAnsweredQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfNewQuestions;
use App\Jobs\User\UpdateUserNumberInProgressQuestions;
use App\SupportQuestion;
use App\SupportQuestionMessage;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SupportQuestionMarkAsSolvedTest extends TestCase
{
	public function testWithoutAjax()
	{
		Bus::fake();

		$supportQuestion = SupportQuestion::factory()->review_starts()->create();

		$user = $supportQuestion->status_changed_user;
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->actingAs($user)
			->get(route('support_questions.solve', $supportQuestion))
			->assertSessionHasNoErrors()
			->assertRedirect(route('support_questions.unsolved'))
			->assertSessionHas('success', __('Thank you! You marked the support question as resolved'));

		$supportQuestion->refresh();

		$this->assertTrue($supportQuestion->isAccepted());

		Bus::assertDispatched(UpdateNumberInProgressQuestions::class);
		Bus::assertDispatched(UpdateNumberOfAnsweredQuestions::class);
		Bus::assertNotDispatched(UpdateNumberOfNewQuestions::class);
		Bus::assertDispatched(UpdateUserNumberInProgressQuestions::class, function ($job) use ($supportQuestion) {
			return $supportQuestion->status_changed_user->is($job->user);
		});
	}

	public function testWithAjax()
	{
		$supportQuestion = SupportQuestion::factory()->review_starts()->create();

		$user = $supportQuestion->status_changed_user;
		$user->group->reply_to_support_service = true;
		$user->push();

		$this->actingAs($user)
			->ajax()
			->get(route('support_questions.solve', $supportQuestion))
			->assertSessionHasNoErrors()
			->assertViewIs('support_question.status')
			->assertViewHas('item', $supportQuestion);

		$supportQuestion->refresh();

		$this->assertTrue($supportQuestion->isAccepted());
	}

	public function testIfAuthUserCreator()
	{
		$supportQuestion = SupportQuestion::factory()->review_starts()->with_message()->create();

		$user = $supportQuestion->create_user;

		$message = factory(SupportQuestionMessage::class)
			->make();

		$supportQuestion->messages()->save($message);
		$supportQuestion->latest_message_id = $message->id;
		$supportQuestion->save();

		$this->actingAs($user)
			->get(route('support_questions.solve', $supportQuestion))
			->assertSessionHasNoErrors()
			->assertRedirect(route('support_questions.show', $supportQuestion))
			->assertSessionHas('success', __('Thank you! You marked the support question as resolved'));

		$supportQuestion->refresh();

		$this->assertTrue($supportQuestion->isAccepted());
	}
}
