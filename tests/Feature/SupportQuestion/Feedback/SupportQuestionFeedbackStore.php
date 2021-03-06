<?php

namespace Tests\Feature\SupportQuestion\Feedback;

use App\FeedbackSupportResponses;
use App\SupportQuestion;
use Tests\TestCase;

class SupportQuestionFeedbackStore extends TestCase
{
    public function testOk()
    {
        $question = SupportQuestion::factory()->accepted()->create();

        $user = $question->create_user;

        $feedbackNew = FeedbackSupportResponses::factory()
            ->make();

        $this->actingAs($user)
            ->post(route('support_questions.feedbacks.store', $question), $feedbackNew->toArray())
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas('success', __('Thank you for your feedback!'));

        $feedback = $question->feedback;

        $this->assertEquals($feedbackNew->text, $feedback->text);
        $this->assertEquals($feedbackNew->face_reaction, $feedback->face_reaction);
    }
}
