<?php

namespace Tests\Feature\SupportQuestion;

use App\SupportQuestion;
use App\SupportQuestionMessage;
use App\User;
use Tests\TestCase;

class SupportQuestionSolvePolicyTest extends TestCase
{
    public function testCanIfUserHasPermissionAndStartedReview()
    {
        $supportQuestion = SupportQuestion::factory()
            ->review_starts()
            ->create([
                'status_changed_user_id' => User::factory()->with_user_group()->admin()
            ]);

        $user = $supportQuestion->status_changed_user;
        $user->group->reply_to_support_service = true;
        $user->push();

        $this->assertTrue($user->can('solve', $supportQuestion));
    }

    public function testCantIfDoesntHavePermission()
    {
        $supportQuestion = SupportQuestion::factory()->create();

        $user = User::factory()->create();
        $user->group->reply_to_support_service = false;
        $user->push();

        $this->assertFalse($user->can('solve', $supportQuestion));
    }

    public function testCantSolveIfAccepted()
    {
        $supportQuestion = SupportQuestion::factory()->accepted()->create();

        $user = User::factory()->create();
        $user->group->reply_to_support_service = true;
        $user->push();

        $this->assertFalse($user->can('solve', $supportQuestion));
    }

    public function testCantIfNotStartedReview()
    {
        $supportQuestion = SupportQuestion::factory()->sent_for_review()->create();

        $user = User::factory()->create();
        $user->group->reply_to_support_service = true;
        $user->push();

        $this->assertFalse($user->can('solve', $supportQuestion));
    }

    public function testCanIfUserThatCreatedRequestAndLastMessageIsNotCreatorOfRequest()
    {
        $supportQuestion = SupportQuestion::factory()->sent_for_review()->create();

        $user = $supportQuestion->create_user;

        $message = SupportQuestionMessage::factory()
            ->make();

        $supportQuestion->messages()->save($message);
        $supportQuestion->latest_message_id = $message->id;
        $supportQuestion->save();

        $this->assertTrue($user->can('solve', $supportQuestion));
    }

    public function testCantIfUserThatCreatedRequestAndLastMessageIsCreatorOfRequest()
    {
        $supportQuestion = SupportQuestion::factory()->sent_for_review()->create();

        $user = $supportQuestion->create_user;

        $message = SupportQuestionMessage::factory()
            ->make(['create_user_id' => $user->id]);

        $supportQuestion->messages()->save($message);
        $supportQuestion->latest_message_id = $message->id;
        $supportQuestion->save();

        $this->assertFalse($user->can('solve', $supportQuestion));
    }
}
