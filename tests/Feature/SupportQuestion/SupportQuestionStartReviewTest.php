<?php

namespace Tests\Feature\SupportQuestion;

use App\Jobs\SupportQuestion\UpdateNumberInProgressQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfAnsweredQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfNewQuestions;
use App\SupportQuestion;
use App\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SupportQuestionStartReviewTest extends TestCase
{
	public function testWithoutAjax()
	{
		Bus::fake();

		$supportQuestion = factory(SupportQuestion::class)
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$supportQuestion->status_changed_user_id = $user->id;
		$supportQuestion->push();
		$supportQuestion->refresh();

		$this->actingAs($user)
			->get(route('support_questions.start_review', $supportQuestion))
			->assertSessionHasNoErrors()
			->assertRedirect(route('support_questions.show', $supportQuestion))
			->assertSessionHas('success', __('You have started reviewing the question'));

		$supportQuestion->refresh();

		$this->assertTrue($supportQuestion->isReviewStarts());

		Bus::assertDispatched(UpdateNumberInProgressQuestions::class);
		Bus::assertNotDispatched(UpdateNumberOfAnsweredQuestions::class);
		Bus::assertDispatched(UpdateNumberOfNewQuestions::class);
	}

	public function testWithAjax()
	{
		$supportQuestion = factory(SupportQuestion::class)
			->create();

		$user = factory(User::class)
			->states('admin')
			->create();

		$supportQuestion->status_changed_user_id = $user->id;
		$supportQuestion->push();
		$supportQuestion->refresh();

		$this->actingAs($user)
			->ajax()
			->get(route('support_questions.start_review', $supportQuestion))
			->assertOk()
			->assertSessionHasNoErrors()
			->assertViewIs('support_question.status')
			->assertViewHas('item', $supportQuestion);

		$supportQuestion->refresh();

		$this->assertTrue($supportQuestion->isReviewStarts());
	}
}
