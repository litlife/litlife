<?php

namespace Tests\Feature\Collection\Comment;

use App\Collection;
use App\Comment;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class CollectionCommentCreateTest extends TestCase
{
    public function testAddComment()
    {
        $user = User::factory()->create();
        $user->group->add_comment = true;
        $user->push();

        $collection = Collection::factory()->create(['who_can_comment' => 'everyone']);

        $text = $this->faker->realText(200);

        $response = $this->actingAs($user)
            ->post(route('comments.store', ['commentable_type' => '18', 'commentable_id' => $collection->id]), [
                'bb_text' => $text
            ]);

        $comment = $user->comments()->first();

        $response->assertRedirect(route('comments.go', $comment))
            ->assertSessionHasNoErrors();

        $this->assertNotNull($comment);
        $this->assertEquals($text, $comment->bb_text);

        $response->assertRedirect(route('comments.go', $comment));

        $collection->refresh();

        $this->assertEquals(1, $collection->comments_count);
    }

    public function testReplyCreateIsOk()
    {
        $comment = Comment::factory()->collection()->create();

        $collection = $comment->commentable;
        $collection->who_can_comment = 'everyone';
        $collection->save();
        $collection->refresh();

        $user = User::factory()->admin()->create();

        $this->assertTrue($user->can('commentOn', $collection));
        $this->assertTrue($user->can('reply', $comment));

        $this->actingAs($user)
            ->get(route('comments.create', [
                'commentable_type' => $comment->commentable_type,
                'commentable_id' => $comment->commentable_id,
                'parent' => $comment->id
            ]))
            ->assertOk();
    }

    public function testReplyStoreIsOk()
    {
        $comment = Comment::factory()->collection()->create();

        $collection = $comment->commentable;
        $collection->who_can_comment = 'everyone';
        $collection->save();
        $collection->refresh();

        $user = User::factory()->admin()->create();

        $text = Str::random(10);

        $this->actingAs($user)
            ->post(route('comments.store', [
                'commentable_type' => $comment->commentable_type,
                'commentable_id' => $comment->commentable_id,
                'parent' => $comment->id
            ]), ['bb_text' => $text])
            ->assertRedirect();

        $reply = $comment->descendants($comment->id)->first();

        $this->assertNotNull($reply);
        $this->assertEquals($text, $reply->bb_text);
    }
}
