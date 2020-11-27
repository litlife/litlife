<?php

namespace Tests\Feature\Artisan;

use App\Jobs\SupportQuestion\UpdateNumberInProgressQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfAnsweredQuestions;
use App\SupportQuestion;
use App\SupportQuestionMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Bus;
use Tests\TestCase;

class AutoSolveSupportQuestionTest extends TestCase
{
    public function testDontSolveIfTimeHasntPassed()
    {
        $question = SupportQuestion::factory()
            ->review_starts()
            ->with_message()
            ->create();

        $message = SupportQuestionMessage::factory()
            ->make();

        $question->messages()->save($message);

        $question->upadateLatestMessage();
        $question->save();

        $message->refresh();

        $days = 7;

        $this->artisan('support_question:solve', ['days' => $days, 'id' => $question->id]);

        $question->refresh();

        $this->assertTrue($question->isReviewStarts());
    }

    public function testSolveIfTimePassed()
    {
        Bus::fake();

        $question = SupportQuestion::factory()
            ->review_starts()
            ->with_message()
            ->create();

        $message = SupportQuestionMessage::factory()
            ->make();

        $question->messages()->save($message);

        $question->upadateLatestMessage();
        $question->save();

        $message->refresh();

        $days = 7;

        $this->travelTo(now()->addDays($days)->addHour());

        $this->artisan('support_question:solve', ['days' => $days, 'id' => $question->id]);

        $question->refresh();

        $this->assertTrue($question->isAccepted());

        Bus::assertDispatched(UpdateNumberOfAnsweredQuestions::class);
        Bus::assertDispatched(UpdateNumberInProgressQuestions::class);
    }

    public function testDontSolveIfReviewNotStarts()
    {
        $question = SupportQuestion::factory()
            ->sent_for_review()
            ->with_message()
            ->create();

        $message = SupportQuestionMessage::factory()
            ->make();

        $question->messages()->save($message);

        $question->upadateLatestMessage();
        $question->save();

        $message->refresh();

        $days = 7;

        $this->travelTo(now()->addDays($days)->addHour());

        $this->artisan('support_question:solve', ['days' => $days, 'id' => $question->id]);

        $question->refresh();

        $this->assertTrue($question->isSentForReview());
    }

    public function testDontSolveLatestUserCreatorOfQuestion()
    {
        $question = SupportQuestion::factory()
            ->review_starts()
            ->with_message()
            ->create();

        $days = 7;

        $this->travelTo(now()->addDays($days)->addHour());

        $this->artisan('support_question:solve', ['days' => $days, 'id' => $question->id]);

        $question->refresh();

        $this->assertTrue($question->isReviewStarts());
    }
}
