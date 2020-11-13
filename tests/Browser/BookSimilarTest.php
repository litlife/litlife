<?php

namespace Tests\Browser;

use App\BookSimilarVote;
use Tests\DuskTestCase;

class BookSimilarTest extends DuskTestCase
{
    public function testUnclickSimilar()
    {
        $this->browse(function ($browser) {

            $bookSimilarVote = BookSimilarVote::factory()->create(['vote' => 1]);

            $book = $bookSimilarVote->book;
            $user = $bookSimilarVote->create_user;

            $user->group->book_similar_vote = true;
            $user->push();

            $other_book = $bookSimilarVote->other_book;

            $browser->loginAs($user)
                ->resize(1000, 1000)
                ->visit(route('books.show', $bookSimilarVote->book))
                ->whenAvailable('#collapse-similar-books', function ($collapseSimilarBooks) use ($other_book) {

                    $collapseSimilarBooks->with('.similars_item[data-other-book-id="'.$other_book->id.'"]', function ($item) {
                        $item->assertPresent('.similar.active')
                            ->assertMissing('.not_similar.active')
                            ->press('.similar');
                    })->waitFor('.loading-cap[data-other-book-id="'.$other_book->id.'"]')
                        ->waitUntilMissing('.loading-cap[data-other-book-id="'.$other_book->id.'"]')
                        ->with('.similars_item[data-other-book-id="'.$other_book->id.'"]', function ($item) {
                            $item->assertMissing('.similar.active')
                                ->assertMissing('.not_similar.active');
                        });
                });

            $books_similar = $book->similars()
                ->havingRaw('SUM("vote") > 0')
                ->orderBy("sum", "desc")
                ->get();

            $this->assertTrue($books_similar->isEmpty());
        });
    }

    public function testClickNotSimilar()
    {
        $this->browse(function ($browser) {

            $bookSimilarVote = BookSimilarVote::factory()->create(['vote' => 1]);

            $book = $bookSimilarVote->book;
            $user = $bookSimilarVote->create_user;

            $user->group->book_similar_vote = true;
            $user->push();

            $other_book = $bookSimilarVote->other_book;

            $browser->loginAs($user)
                ->resize(1000, 1000)
                ->visit(route('books.show', $book))
                ->whenAvailable('#collapse-similar-books', function ($collapseSimilarBooks) use ($other_book) {

                    $collapseSimilarBooks->with('.similars_item[data-other-book-id="'.$other_book->id.'"]', function ($item) {
                        $item->press('.not_similar');
                    })->waitFor('.loading-cap[data-other-book-id="'.$other_book->id.'"]')
                        ->waitUntilMissing('.loading-cap[data-other-book-id="'.$other_book->id.'"]')
                        ->with('.similars_item[data-other-book-id="'.$other_book->id.'"]', function ($item) {
                            $item->assertMissing('.similar.active')
                                ->assertPresent('.not_similar.active');
                        });
                });

            $bookSimilarVote->refresh();

            $this->assertEquals('-1', $bookSimilarVote->vote);

            $books_similar = $book->similars()
                ->havingRaw('SUM("vote") > 0')
                ->orderBy("sum", "desc")
                ->get();

            $this->assertTrue($books_similar->isEmpty());
        });
    }
}
