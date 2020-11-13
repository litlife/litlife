<?php

namespace Tests\Feature\Collection\Comment;

use App\Collection;
use App\Comment;
use App\Enums\StatusEnum;
use App\User;
use Tests\TestCase;

class CollectionCommentIndexTest extends TestCase
{
    public function testCommentsHttp()
    {
        $user = User::factory()->admin()->create();

        $comment = Comment::factory()->collection()->create();

        $collection = $comment->commentable;
        $collection->status = StatusEnum::Accepted;
        $collection->who_can_add = 'everyone';
        $collection->who_can_comment = 'everyone';
        $collection->save();
        $collection->refresh();

        $this->actingAs($user)
            ->get(route('collections.comments', $collection))
            ->assertOk()
            ->assertSeeText($comment->text)
            ->assertSee(route('comments.store', [
                'commentable_type' => 18,
                'commentable_id' => $collection->id
            ]));
    }

    public function testCommentsOkIfUserGuestCanSeeEveryone()
    {
        $collection = Collection::factory()->accepted()->create();

        $this->get(route('collections.comments', $collection))
            ->assertOk();
    }

    public function testCommentsForbiddenIfUserGuestCanSeeMe()
    {
        $collection = Collection::factory()->private()->create();

        $this->get(route('collections.comments', $collection))
            ->assertForbidden();
    }
}
