<?php

namespace Tests\Feature\Complain;

use App\Comment;
use App\Complain;
use App\User;
use Tests\TestCase;

class ComplainStoreTest extends TestCase
{
    public function testStore()
    {
        $user = User::factory()->create();
        $user->group->complain = true;
        $user->push();

        $comment = Comment::factory()->create();

        $count = Complain::getCachedOnModerationCount();

        $text = $this->faker->realText();

        $response = $this->actingAs($user)
            ->post(route('complains.save', ['type' => 'comment', 'id' => $comment->id]), [
                'text' => $text
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas(['success' => __('complain.complaint_sent')]);

        $complain = $comment->complaints()->first();

        $response->assertRedirect(route('complaints.show', ['complain' => $complain]));

        $this->assertEquals(($count + 1), Complain::getCachedOnModerationCount());

        $this->assertNotNull($complain);
        $this->assertEquals($text, $complain->text);
        $this->assertEquals($user->id, $complain->create_user_id);
        $this->assertTrue($complain->isSentForReview());
    }

    public function testStoreIfOtherExists()
    {
        $complain = Complain::factory()->accepted()->create();

        $user = $complain->create_user;
        $user->group->complain = true;
        $user->push();

        $complainable = $complain->complainable;

        $text = $this->faker->realText();

        $response = $this->actingAs($user)
            ->post(route('complains.save', ['type' => $complain->getComplainableName(), 'id' => $complainable->id]), [
                'text' => $text
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas(['success' => __('complain.complaint_sent')]);

        $this->assertEquals(2, $complainable->complaints()->count());
    }
}
