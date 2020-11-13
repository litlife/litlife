<?php

namespace Tests\Feature\Book\Similar;

use App\Book;
use App\User;
use Tests\TestCase;

class BookSimilarVoteTest extends TestCase
{
    public function testVoteHttp()
    {
        $book = Book::factory()->create();
        $book2 = Book::factory()->create();

        $user = User::factory()->create();
        $user->group->book_similar_vote = true;
        $user->push();

        $response = $this->actingAs($user)
            ->get(
                route('books.similar.vote', [
                    'book' => $book->id,
                    'otherBook' => $book2->id,
                    'vote' => 1
                ]));

        $response->assertSessionHasNoErrors()
            ->assertStatus(201);

        $this->assertEquals(1, $book->fresh()->similars->first()->sum);

        $response = $this->actingAs($user)
            ->get(
                route('books.similar.vote', [
                    'book' => $book->id,
                    'otherBook' => $book2->id,
                    'vote' => 1
                ]))
            ->assertSessionHasNoErrors()
            ->assertOk();

        $this->assertEquals(0, $book->fresh()->similars->first()->sum);

        $response = $this->actingAs($user)
            ->get(
                route('books.similar.vote', [
                    'book' => $book->id,
                    'otherBook' => $book2->id,
                    'vote' => 1
                ]))
            ->assertSessionHasNoErrors()
            ->assertOk();

        $this->assertEquals(1, $book->fresh()->similars->first()->sum);

        $response = $this->actingAs($user)
            ->get(
                route('books.similar.vote', [
                    'book' => $book->id,
                    'otherBook' => $book2->id,
                    'vote' => -1
                ]))
            ->assertSessionHasNoErrors()
            ->assertOk();

        $this->assertEquals(-1, $book->fresh()->similars->first()->sum);
    }
}
