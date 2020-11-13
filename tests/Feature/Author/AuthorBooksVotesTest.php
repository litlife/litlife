<?php

namespace Tests\Feature\Author;

use App\Author;
use Tests\TestCase;

class AuthorBooksVotesTest extends TestCase
{
    public function testBookVotesHttp()
    {
        $author = Author::factory()->with_book_vote()->create();

        $book = $author->books()->get()->first();
        $vote = $book->votes()->get()->first();
        $user = $vote->create_user;

        $this->get(route('authors.books_votes', ['author' => $author]))
            ->assertOk()
            ->assertSeeText($user->nick);
    }

    public function testBookVotesHttpIfUserDeleted()
    {
        $author = Author::factory()->with_book_vote()->create();

        $book = $author->books()->get()->first();
        $vote = $book->votes()->get()->first();
        $user = $vote->create_user;

        $user->delete();

        $this->get(route('authors.books_votes', ['author' => $author]))
            ->assertOk()
            ->assertSeeText(__('User is not found'));
    }
}
