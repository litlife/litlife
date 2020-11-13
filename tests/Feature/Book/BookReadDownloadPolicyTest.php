<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\User;
use App\UserPurchase;
use Tests\TestCase;

class BookReadDownloadPolicyTest extends TestCase
{
    public function testCanReadOrDownloadIfAuthorPolicy()
    {
        $author = Author::factory()->with_author_manager()->with_book_for_sale()->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();

        $this->assertTrue($user->can('read', $book));
        $this->assertTrue($user->can('download', $book));
        $this->assertTrue($user->can('read_or_download', $book));
    }

    public function testReadDownloadIfBookForSalePolicy()
    {
        $book = Book::factory()->with_section()->create();

        $user = User::factory()->create();

        $this->assertFalse($user->can('read', $book));
        $this->assertFalse($user->can('download', $book));
        $this->assertFalse($user->can('read_or_download', $book));

        $purchase = UserPurchase::factory()->create([
            'buyer_user_id' => $user->id,
            'purchasable_id' => $book->id,
            'purchasable_type' => 'book'
        ]);

        $book->refresh();

        $this->assertEquals($purchase->purchasable->id, $book->id);

        $this->assertTrue($user->can('read', $book));
        $this->assertTrue($user->can('download', $book));
        $this->assertTrue($user->can('read_or_download', $book));
    }
}
