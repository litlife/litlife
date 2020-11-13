<?php

namespace Tests\Feature\Book\BookGroup;

use App\Book;
use App\Comment;
use App\Jobs\Book\BookGroupJob;
use App\User;
use Tests\TestCase;

class BookGroupCommentsTest extends TestCase
{
    public function testAddComment()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        BookGroupJob::dispatch($mainBook, $minorBook);

        $user = User::factory()->create();
        $user->group->add_comment = true;
        $user->push();

        $this->actingAs($user)
            ->post(route('comments.store', [
                'commentable_type' => 'book',
                'commentable_id' => $minorBook->id
            ]),
                [
                    'bb_text' => $this->faker->realText()
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $comment = $user->comments()->first();

        $this->assertEquals($user->id, $comment->create_user_id);
        $this->assertEquals($mainBook->id, $comment->commentable_id);
        $this->assertEquals($minorBook->id, $comment->origin_commentable_id);
    }

    public function testChildCommentHasSameCommentableTypeAndId()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $comment = Comment::factory()->book()->create(['commentable_id' => $mainBook->id]);

        $comment2 = Comment::factory()->book()->create((['commentable_id' => $minorBook->id]));

        BookGroupJob::dispatch($mainBook, $minorBook);

        $user = User::factory()->create();
        $user->group->add_comment = true;
        $user->push();

        $this->actingAs($user)
            ->post(route('comments.store', [
                'commentable_type' => 'book',
                'commentable_id' => $minorBook->id,
                'parent' => $comment2->id
            ]),
                [
                    'bb_text' => $this->faker->realText()
                ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $comment2->refresh();

        $comment3 = $user->comments()->first();

        $this->assertNotNull($comment3);
        $this->assertEquals($user->id, $comment3->create_user_id);
        $this->assertEquals($mainBook->id, $comment3->commentable_id);
        $this->assertEquals($minorBook->id, $comment3->origin_commentable_id);

        $this->assertEquals($comment2->commentable_type, $comment3->commentable_type);
        $this->assertEquals($comment2->commentable_id, $comment3->commentable_id);
        $this->assertEquals($comment2->origin_commentable_id, $comment3->origin_commentable_id);
    }
}
