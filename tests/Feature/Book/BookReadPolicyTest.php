<?php

namespace Tests\Feature\Book;

use App\Book;
use App\User;
use App\UserPurchase;
use Tests\TestCase;

class BookReadPolicyTest extends TestCase
{
    public function testCanReadIfBookPurchased()
    {
        $book = Book::factory()->with_section()->create();

        $user = User::factory()->create();

        $purchase = UserPurchase::factory()->create([
            'buyer_user_id' => $user->id,
            'purchasable_id' => $book->id,
            'purchasable_type' => 'book'
        ]);

        $book->refresh();

        $this->assertEquals($purchase->purchasable->id, $book->id);

        $this->assertTrue($user->can('read', $book));
    }

    public function testCantReadBookIfPurchaseCanceled()
    {
        $book = Book::factory()->with_section()->create(['price' => 100]);

        $user = User::factory()->create();

        $purchase = UserPurchase::factory()
            ->canceled()
            ->create([
                'buyer_user_id' => $user->id,
                'purchasable_id' => $book->id,
                'purchasable_type' => 'book'
            ]);

        $book->refresh();

        $this->assertEquals($purchase->purchasable->id, $book->id);

        $this->assertFalse($user->can('read', $book));
    }

    public function testCanIfUserBoughtBookAndItIsRemovedFromSale()
    {
        $book = Book::factory()
            ->with_section()
            ->removed_from_sale()
            ->withReadAccess()
            ->create();

        $this->assertTrue($book->isRejected());

        $user = User::factory()
            ->create();

        $purchase = UserPurchase::factory()
            ->create([
                'buyer_user_id' => $user->id,
                'purchasable_id' => $book->id,
                'purchasable_type' => 'book'
            ]);

        $section = $book->sections()->chapter()->first();

        $this->assertTrue($user->can('read', $book));
        $this->assertTrue($user->can('view_read_button', $book));
        $this->assertTrue($user->can('view', $section));

        $page = $section->pages()->first();

        $this->assertTrue($user->can('view', $page));
    }

    public function testCantIfBookRemovedFromSale()
    {
        $book = Book::factory()
            ->with_section()
            ->removed_from_sale()
            ->withReadAccess()
            ->create();

        $this->assertTrue($book->isRejected());

        $user = User::factory()
            ->create();

        $section = $book->sections()->chapter()->first();

        $this->assertFalse($user->can('read', $book));
        $this->assertFalse($user->can('view_read_button', $book));
        $this->assertFalse($user->can('view', $section));

        $page = $section->pages()->first();

        $this->assertFalse($user->can('view', $page));
    }
}