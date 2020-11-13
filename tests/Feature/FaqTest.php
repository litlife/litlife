<?php

namespace Tests\Feature;

use App\Enums\VariablesEnum;
use App\Forum;
use App\User;
use App\Variable;
use Tests\TestCase;

class FaqTest extends TestCase
{
    public function testIsOk()
    {
        $this->get(route('faq'))
            ->assertOk();
    }

    public function testStoreQuestion()
    {
        $id = Variable::where('name', VariablesEnum::ForumOfQuestions)
            ->firstOrFail()
            ->value;

        $forum = Forum::findOrFail($id);

        $name = $this->faker->realText(100);
        $text = $this->faker->realText(200);

        $user = User::factory()->create();
        $user->forum_message_count = $forum->min_message_count + 10;
        $user->group->add_forum_topic = true;
        $user->push();

        $response = $this->actingAs($user)
            ->post(route('questions.store'), [
                'name' => $name,
                'bb_text' => $text,
                'notify_about_responses' => '1'
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $topic = $user->created_topics()->first();

        $post = $topic->posts()->first();

        $this->assertNotNull($topic);
        $this->assertEquals($topic->name, $name);
        $this->assertEquals($post->bb_text, $text);

        $this->assertTrue($post->isFixed());

        $subscription = $topic->user_subscriptions()
            ->first();

        $this->assertNotNull($subscription);
        $this->assertEquals($user->id, $subscription->user_id);
        $this->assertEquals($topic->id, $subscription->topic_id);
    }
}
