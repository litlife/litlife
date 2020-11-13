<?php

namespace Tests\Feature\Book\Keyword;

use App\Book;
use App\BookKeyword;
use App\User;
use Tests\TestCase;

class BookKeywordOnModerationTest extends TestCase
{
    public function testSeeBookTitleIfKeywordSentForReview()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_moderate = true;
        $user->push();

        $book = Book::factory()->create();
        $book->statusSentForReview();
        $book->save();
        $book->refresh();

        $book_keyword = BookKeyword::factory()->create(['book_id' => $book->id]);
        $book_keyword->statusSentForReview();
        $book_keyword->save();

        $response = $this->actingAs($user)
            ->get(route('book_keywords.on_moderation'))
            ->assertOk()
            ->assertSeeText($book->title)
            ->assertSeeText($book_keyword->keyword->text);
    }
}
