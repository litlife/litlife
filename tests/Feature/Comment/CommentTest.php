<?php

namespace Tests\Feature\Comment;

use App\Author;
use App\Book;
use App\BookStatus;
use App\BookVote;
use App\Comment;
use App\Jobs\User\UpdateUserCommentsCount;
use App\User;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class CommentTest extends TestCase
{
    public function testFulltextSearch()
    {
        $author = Comment::FulltextSearch('Время&—&детство!')->get();

        $this->assertTrue(true);
    }

    public function testCommentsOnCheck()
    {
        $user = User::factory()->create();
        $user->group->check_post_comments = true;
        $user->push();

        $comment = Comment::factory()->create();
        $comment->statusSentForReview();
        $comment->save();
        $comment->refresh();

        $this->assertTrue($comment->isSentForReview());

        $this->actingAs($user)
            ->get(route('comments.on_check'))
            ->assertOk()
            ->assertSeeText($comment->text);
    }

    public function testUserReadedBooksHttp()
    {
        $user = User::factory()->create();

        $comment = Comment::factory()->create();

        $this->actingAs($user)
            ->get(route('users.books.readed.comments', $user))
            ->assertDontSee($comment->text);

        $book_status = BookStatus::factory()->create([
            'book_id' => $comment->commentable->id,
            'user_id' => $user->id,
            'status' => 'read_now'
        ]);

        $this->actingAs($user)
            ->get(route('users.books.readed.comments', $user))
            ->assertDontSee($comment->text);

        $book_status->status = 'readed';
        $book_status->save();

        $this->actingAs($user)
            ->get(route('users.books.readed.comments', $user))
            ->assertSee($comment->text);
    }

    public function testViewInUserCommentList()
    {
        $comment = Comment::factory()->create();
        $comment->statusSentForReview();
        $comment->save();

        UpdateUserCommentsCount::dispatch($comment->create_user);

        $this->assertEquals(1, $comment->create_user->comment_count);

        $user = User::factory()->create();

        $this->actingAs($comment->create_user)
            ->get(route('users.books.comments', ['user' => $comment->create_user->id]))
            ->assertOk()
            ->assertSeeText($comment->text);

        $this->actingAs($user)
            ->get(route('users.books.comments', ['user' => $comment->create_user->id]))
            ->assertOk()
            ->assertDontSeeText($comment->text)
            ->assertSeeText(trans_choice('comment.on_check', 1));
    }

    public function testBBEmpty()
    {
        $comment = Comment::factory()->create();

        $this->expectException(QueryException::class);

        $comment->bb_text = '';
        $comment->save();
    }

    public function testRelationUserBookVote()
    {
        $user = User::factory()->create();

        $comment = Comment::factory()->create([
            'commentable_type' => 'book',
            'create_user_id' => $user->id
        ]);

        $book = $comment->commentable;

        $this->assertInstanceOf(Book::class, $book);

        $vote = BookVote::factory()->create([
            'book_id' => $book->id,
            'create_user_id' => $user->id
        ])->fresh();

        $vote2 = BookVote::factory()->create([
            'book_id' => $book->id
        ])->fresh();

        $vote3 = BookVote::factory()->create([
            'create_user_id' => $user->id
        ])->fresh();

        $comment->refresh();

        $comments = Comment::where('id', $comment->id)
            ->with('userBookVote')
            ->get();

        $vote4 = $comments->first()->userBookVote;

        $this->assertEquals($vote, $vote4);
    }

    public function testUpperCaseLettersCount()
    {
        $text = '[I]текст[/I][b]Текст Текст Текст[/b]';

        $comment = Comment::factory()->create(['bb_text' => $text]);

        $this->assertEquals(3, $comment->getUpperCaseCharactersCount($comment->getContent()));
        $this->assertEquals(15, $comment->getUpperCaseLettersPercent($comment->getContent()));

        $text = ' ТЕКСТ текст';

        $comment = Comment::factory()->create(['bb_text' => $text]);

        $this->assertEquals(5, $comment->getUpperCaseCharactersCount($comment->getContent()));
        $this->assertEquals(50, $comment->getUpperCaseLettersPercent($comment->getContent()));

        $text = ' ТЕКСТ ТЕКСТ тек';

        $comment = Comment::factory()->create(['bb_text' => $text]);

        $this->assertEquals(10, $comment->getUpperCaseCharactersCount($comment->getContent()));
        $this->assertEquals(77, $comment->getUpperCaseLettersPercent($comment->getContent()));

        $text = ' :) ';

        $comment = Comment::factory()->create(['bb_text' => $text]);

        $this->assertEquals(0, $comment->getUpperCaseCharactersCount($comment->getContent()));
        $this->assertEquals(0, $comment->getUpperCaseLettersPercent($comment->getContent()));
    }

    public function testSentForReviewIfExternalLinksAndLackOfCommentsCount()
    {
        $text = 'текст [url]http://example.com/test[/url] текст [url]http://example.com/test[/url]';

        $comment = Comment::factory()->create(['bb_text' => $text]);

        $this->assertTrue($comment->isSentForReview());
    }

    public function testAcceptedIfExternalLinksAndEnoughOfCommentsCount()
    {
        $user = User::factory()->create();
        $user->comment_count = 100;
        $user->save();

        $text = 'текст [url]http://example.com/test[/url] текст [url]http://example.com/test[/url]';

        $comment = Comment::factory()->create(['create_user_id' => $user->id, 'bb_text' => $text]);

        $this->assertTrue($comment->isAccepted());
    }

    public function testAcceptedIfExternalLinksAndEnoughOfPostsCount()
    {
        $user = User::factory()->create();
        $user->forum_message_count = 100;
        $user->save();

        $text = 'текст [url]http://example.com/test[/url] текст [url]http://example.com/test[/url]';

        $comment = Comment::factory()->create(['create_user_id' => $user->id, 'bb_text' => $text]);

        $this->assertTrue($comment->isAccepted());
    }

    public function testSentForReviewIfTextUpperCase()
    {
        $text = 'ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ТЕКСТ ';

        $comment = Comment::factory()->create(['bb_text' => $text]);

        $this->assertTrue($comment->isSentForReview());
    }

    public function testAcceptedIfTextUpperCase2()
    {
        $text = 'ТЕ';

        $comment = Comment::factory()->create(['bb_text' => $text]);

        $this->assertTrue($comment->isAccepted());
    }

    public function testPerPage2()
    {
        $response = $this->get(route('home.latest_comments', ['per_page' => 5]))
            ->assertOk();

        $this->assertEquals(10, $response->original->gatherData()['comments']->perPage());

        $response = $this->get(route('home.latest_comments', ['per_page' => 200]))
            ->assertOk();

        $this->assertEquals(100, $response->original->gatherData()['comments']->perPage());
    }

    public function testPerPage()
    {
        $author = Author::factory()->create();

        $response = $this->get(route('authors.comments', ['author' => $author, 'per_page' => 5]))
            ->assertOk();

        $this->assertEquals(10, $response->original->gatherData()['comments']->perPage());

        $response = $this->get(route('authors.comments', ['author' => $author, 'per_page' => 200]))
            ->assertOk();

        $this->assertEquals(100, $response->original->gatherData()['comments']->perPage());
    }

    public function testSetGetParentComment()
    {
        $comment = Comment::factory()->create();

        $comment2 = new Comment();
        $comment2->parent = $comment;

        $this->assertTrue($comment2->parent->is($comment));
    }

    public function testOriginCommentableRelation()
    {
        $comment = Comment::factory()->book()->create();

        $book = $comment->commentable;

        $this->assertEquals($book->origin_commentable_id, $book->commentable_id);
        $this->assertInstanceOf(Book::class, $comment->commentable);
        $this->assertInstanceOf(Book::class, $comment->originCommentable);
        $this->assertEquals($comment->commentable->id, $comment->originCommentable->id);

        $comment->load(['commentable', 'originCommentable']);

        $this->assertEquals($book->origin_commentable_id, $book->commentable_id);
        $this->assertInstanceOf(Book::class, $comment->commentable);
        $this->assertInstanceOf(Book::class, $comment->originCommentable);
        $this->assertEquals($comment->commentable->id, $comment->originCommentable->id);
    }

    /*
        public function testForbidBBCodeTags()
        {
            $text = '[b][size]текст[/size][/b]';

            $comment = new Comment;
            $comment->setForbidTags(['size']);
            $comment->setBBCode($text);

            $this->assertEquals('[b]текст[/b]', $comment->getBBCode());

            $text = '[size=5][b]текст[/b][/size]';

            $comment = new Comment;
            $comment->setForbidTags(['size']);
            $comment->setBBCode($text);

            $this->assertEquals('[b]текст[/b]', $comment->getBBCode());

            $text = '[code][size=5][b]текст[/b][/size][/code]';

            $comment = new Comment;
            $comment->setForbidTags(['size']);
            $comment->setBBCode($text);

            $this->assertEquals('[code][size=5][b]текст[/b][/size][/code]', $comment->getBBCode());

            $text = '[code][size=5][b][code][size=5]текст[/size][/code][/b][/size][/code]';

            $comment = new Comment;
            $comment->setForbidTags(['size']);
            $comment->setBBCode($text);

            $this->assertEquals('[code][size=5][b][code][size=5]текст[/size][/code][/b][/size][/code]',
                $comment->getBBCode());

            $text = '[code][size=5][b]text[/b][/size][/code]text[code][size=5]текст[/size][/code]';

            $comment = new Comment;
            $comment->setForbidTags(['size']);
            $comment->setBBCode($text);

            $this->assertEquals('[code][size=5][b]text[/b][/size][/code]text[code][size=5]текст[/size][/code]',
                $comment->getBBCode());
        }
    */
}
