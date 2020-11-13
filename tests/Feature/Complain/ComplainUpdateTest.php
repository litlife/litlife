<?php

namespace Tests\Feature\Complain;

use App\Complain;
use App\User;
use Tests\TestCase;

class ComplainUpdateTest extends TestCase
{
    public function testEdit()
    {
        $user = User::factory()->create();

        $complain = Complain::factory()->comment()->sent_for_review()->create();

        $user = $complain->create_user;
        $user->group->complain = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('complains.report', ['type' => $complain->complainable_type, 'id' => $complain->complainable_id]))
            ->assertOk()
            ->assertViewHas('complain', $complain)
            ->assertSeeText($complain->text);
    }

    public function testUpdateIsOk()
    {
        $user = User::factory()->create();

        $complain = Complain::factory()->comment()->sent_for_review()->create();

        $user = $complain->create_user;
        $user->group->complain = true;
        $user->push();

        $text = $this->faker->realText();

        $this->actingAs($user)
            ->post(route('complains.save', ['type' => $complain->complainable_type, 'id' => $complain->complainable_id]), [
                'text' => $text
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas(['success' => __('complain.complaint_was_successfully_edited')]);

        $complain->refresh();

        $this->assertStringContainsString($text, $complain->text);
    }
}
