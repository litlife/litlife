<?php

namespace Tests\Feature\Collection\Comment;

use App\Collection;
use App\Comment;
use App\User;
use Tests\TestCase;

class CollectionCommentDeletePolicyTest extends TestCase
{
    public function testCanIfHasPermission()
    {
        $user = User::factory()->create();
        $user->group->edit_or_delete_your_comments_to_collections = true;
        $user->push();

        $collection = Collection::factory()->create(['create_user_id' => $user->id]);

        $comment = Comment::factory()->make(['create_user_id' => $user->id]);

        $collection->comments()->save($comment);

        $this->assertTrue($user->can('delete', $comment));
    }

    public function testCantIfDoesntHavePermission()
    {
        $user = User::factory()->create();
        $user->group->edit_or_delete_your_comments_to_collections = false;
        $user->push();

        $collection = Collection::factory()->create(['create_user_id' => $user->id]);

        $comment = Comment::factory()->make(['create_user_id' => $user->id]);

        $collection->comments()->save($comment);

        $this->assertFalse($user->can('delete', $comment));
    }
}
