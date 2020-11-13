<?php

namespace Tests\Feature\SupportQuestion;

use App\Jobs\SupportQuestion\UpdateNumberInProgressQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfAnsweredQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfNewQuestions;
use App\Jobs\User\UpdateUserNumberInProgressQuestions;
use App\SupportQuestion;
use App\User;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class SupportQuestionStopReviewTest extends TestCase
{
    public function testWithoutAjax()
    {
        Bus::fake();

        $supportQuestion = SupportQuestion::factory()->review_starts()->create();

        $user = User::factory()->admin()->create();

        $supportQuestion->status_changed_user_id = $user->id;
        $supportQuestion->push();
        $supportQuestion->refresh();

        $this->actingAs($user)
            ->get(route('support_questions.stop_review', $supportQuestion))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('support_questions.unsolved'))
            ->assertSessionHas('success', __('You refused to resolve the question'));

        $supportQuestion->refresh();

        $this->assertTrue($supportQuestion->isSentForReview());

        Bus::assertDispatched(UpdateNumberInProgressQuestions::class);
        Bus::assertNotDispatched(UpdateNumberOfAnsweredQuestions::class);
        Bus::assertDispatched(UpdateNumberOfNewQuestions::class);
        Bus::assertDispatched(UpdateUserNumberInProgressQuestions::class, function ($job) use ($supportQuestion) {
            return $supportQuestion->status_changed_user->is($job->user);
        });
    }

    public function testWithAjax()
    {
        $supportQuestion = SupportQuestion::factory()->review_starts()->create();

        $user = User::factory()->admin()->create();

        $supportQuestion->status_changed_user_id = $user->id;
        $supportQuestion->push();
        $supportQuestion->refresh();

        $this->actingAs($user)
            ->ajax()
            ->get(route('support_questions.stop_review', $supportQuestion))
            ->assertSessionHasNoErrors()
            ->assertViewIs('support_question.status')
            ->assertViewHas('item', $supportQuestion);

        $supportQuestion->refresh();

        $this->assertTrue($supportQuestion->isSentForReview());
    }
}
