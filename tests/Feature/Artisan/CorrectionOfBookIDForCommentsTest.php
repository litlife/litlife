<?php

namespace Tests\Feature\Artisan;

use App\Book;
use App\Comment;
use App\Console\Commands\Fix\CorrectionOfBookIDForComments;
use App\Jobs\Book\BookGroupJob;
use Tests\TestCase;

class CorrectionOfBookIDForCommentsTest extends TestCase
{
    public function testCommand()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        BookGroupJob::dispatch($mainBook, $minorBook);

        $comment = Comment::factory()->book()
            ->create([
                'commentable_id' => $minorBook->id,
                'origin_commentable_id' => $minorBook->id,
            ]);

        $this->artisan('fix:correction_of_book_id_for_comments', ['latest_id' => $comment->id])
            ->expectsOutput('Comment id '.$comment->id)
            ->assertExitCode(0);

        $comment->refresh();

        $this->assertEquals($mainBook->id, $comment->commentable_id);
        $this->assertEquals($minorBook->id, $comment->origin_commentable_id);
    }

    public function testTrue()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        BookGroupJob::dispatch($mainBook, $minorBook);

        $comment = Comment::factory()
            ->book()
            ->create([
                'commentable_id' => $minorBook->id,
                'origin_commentable_id' => $minorBook->id,
            ]);

        $command = new CorrectionOfBookIDForComments;

        $this->assertTrue($command->item($comment));

        $comment->refresh();

        $this->assertEquals($mainBook->id, $comment->commentable_id);
        $this->assertEquals($minorBook->id, $comment->origin_commentable_id);
    }

    public function testFalseIfCommentableIsNotBook()
    {
        $comment = Comment::factory()->collection()->create();

        $command = new CorrectionOfBookIDForComments;

        $this->expectExceptionMessage('Сommentable must be a book');

        $command->item($comment);
    }

    public function testFalseIfOriginCommentableMustBeInGroup()
    {
        $minorBook = Book::factory()->create();

        $comment = Comment::factory()
            ->book()
            ->create([
                'commentable_id' => $minorBook->id,
                'origin_commentable_id' => $minorBook->id,
            ]);

        $command = new CorrectionOfBookIDForComments;

        $this->expectExceptionMessage('Origin commentable must be in group');

        $command->item($comment);
    }

    public function testFalseIfOriginCommentableMustBeNotMainInGroup()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        BookGroupJob::dispatch($mainBook, $minorBook);

        $comment = Comment::factory()
            ->book()
            ->create([
                'commentable_id' => $mainBook->id,
                'origin_commentable_id' => $mainBook->id,
            ]);

        $command = new CorrectionOfBookIDForComments;

        $this->expectExceptionMessage('Origin commentable must be not main in group');

        $command->item($comment);
    }

    public function testFalseIfOriginCommentableMustBeNotCommentable()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $comment = Comment::factory()
            ->book()
            ->create([
                'commentable_id' => $mainBook->id,
                'origin_commentable_id' => $minorBook->id,
            ]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $command = new CorrectionOfBookIDForComments;

        $this->expectExceptionMessage('Main book must be not commentable');

        $command->item($comment);
    }


    public function testFalseOnExceptionOriginCommentableMustBeInstanceOfBook()
    {
        $mainBook = Book::factory()->create();

        $minorBook = Book::factory()->create();

        $comment = Comment::factory()
            ->book()
            ->create([
                'commentable_id' => $mainBook->id,
                'origin_commentable_id' => $minorBook->id,
            ]);

        BookGroupJob::dispatch($mainBook, $minorBook);

        $minorBook->forceDelete();

        $command = new CorrectionOfBookIDForComments;

        $this->expectExceptionMessage('Origin commentable must be instance of book');

        $command->item($comment);
    }
}
