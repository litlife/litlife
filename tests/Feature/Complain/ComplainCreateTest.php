<?php

namespace Tests\Feature\Complain;

use App\Comment;
use App\Complain;
use App\User;
use Tests\TestCase;

class ComplainCreateTest extends TestCase
{
    public function testCreate()
    {
        $user = User::factory()->create();
        $user->group->complain = true;
        $user->push();

        $comment = Comment::factory()->create();

        $this->actingAs($user)
            ->get(route('complains.report', ['type' => 'comment', 'id' => $comment->id]))
            ->assertOk()
            ->assertSeeText(__('complain.text'));
    }

    public function testCreateNewIfOtherAcceptedExists()
    {
        $complain = Complain::factory()->accepted()->create();

        $user = $complain->create_user;
        $user->group->complain = true;
        $user->push();

        $complainable = $complain->complainable;

        $this->actingAs($user)
            ->get(route('complains.report', ['type' => $complain->getComplainableName(), 'id' => $complainable->id]))
            ->assertOk()
            ->assertViewIs('complain.create_edit')
            ->assertViewHas('complain', null)
            ->assertDontSee($complain->text);
    }

    public function testCreateIfReviewStarts()
    {
        $complain = Complain::factory()->review_starts()->create();

        $user = $complain->create_user;
        $user->group->complain = true;
        $user->push();

        $complainable = $complain->complainable;

        $this->actingAs($user)
            ->get(route('complains.report', ['type' => $complain->getComplainableName(), 'id' => $complainable->id]))
            ->assertRedirect(route('complaints.show', ['complain' => $complain]));

        $this->actingAs($user)
            ->followingRedirects()
            ->get(route('complains.report', ['type' => $complain->getComplainableName(), 'id' => $complainable->id]))
            ->assertSeeText(__('Сomplaint is currently under review'));
    }
}
