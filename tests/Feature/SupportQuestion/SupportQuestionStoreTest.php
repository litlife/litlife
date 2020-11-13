<?php

namespace Tests\Feature\SupportQuestion;

use App\Events\NumberOfUnsolvedSupportQuestionsHasChanged;
use App\Jobs\SupportQuestion\UpdateNumberInProgressQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfAnsweredQuestions;
use App\Jobs\SupportQuestion\UpdateNumberOfNewQuestions;
use App\SupportQuestion;
use App\SupportQuestionMessage;
use App\User;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;
use Tests\TestCase;

class SupportQuestionStoreTest extends TestCase
{
    public function testIsOk()
    {
        Bus::fake();

        $user = User::factory()->create();

        $supportQuestionNew = SupportQuestion::factory()
            ->make();

        $supportQuestionMessageNew = SupportQuestionMessage::factory()
            ->make();

        $response = $this->actingAs($user)
            ->post(route('support_questions.store', ['user' => $user]),
                array_merge($supportQuestionNew->toArray(), $supportQuestionMessageNew->toArray()))
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas('success', __('Question to support has been sent successfully'));

        $message = $user->createdSupportMessages()->first();

        $this->assertNotNull($message);

        $supportQuestion = $message->supportQuestion;

        $this->assertEquals($supportQuestionNew->title, $supportQuestion->title);

        $response->assertRedirect(route('support_questions.show', ['support_question' => $supportQuestion->id]));

        $this->assertEquals($supportQuestion->id, $message->support_question_id);
        $this->assertEquals($user->id, $message->create_user_id);
        $this->assertEquals($supportQuestionMessageNew->text, $message->text);

        $this->assertEquals(1, $supportQuestion->number_of_messages);
        $this->assertEquals($message->id, $supportQuestion->latest_message_id);

        Bus::assertNotDispatched(UpdateNumberInProgressQuestions::class);
        Bus::assertNotDispatched(UpdateNumberOfAnsweredQuestions::class);
        Bus::assertDispatched(UpdateNumberOfNewQuestions::class);
    }

    public function testWithoutTitle()
    {
        $user = User::factory()->create();

        $supportQuestionNew = SupportQuestion::factory()
            ->make();

        $supportQuestionMessageNew = SupportQuestionMessage::factory()
            ->make();

        $array = array_merge($supportQuestionNew->toArray(), $supportQuestionMessageNew->toArray());

        unset($array['title']);

        $array['category'] = (string) $array['category'];

        $response = $this->actingAs($user)
            ->post(route('support_questions.store', ['user' => $user]), $array)
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas('success', __('Question to support has been sent successfully'));

        $message = $user->createdSupportMessages()->first();

        $this->assertNotNull($message);

        $supportQuestion = $message->supportQuestion;

        $this->assertEquals(Str::limit($supportQuestionMessageNew->text, 97), $supportQuestion->title);

        $response->assertRedirect(route('support_questions.show', ['support_question' => $supportQuestion->id]));

        $this->assertEquals($supportQuestion->id, $message->support_question_id);
        $this->assertEquals($user->id, $message->create_user_id);
        $this->assertEquals($supportQuestionMessageNew->text, $message->text);

        $this->assertEquals(1, $supportQuestion->number_of_messages);
        $this->assertEquals($message->id, $supportQuestion->latest_message_id);

        $this->assertEquals($supportQuestionNew->category, $supportQuestion->category);
    }
}
