<?php

namespace Tests\Feature\Author;

use App\Author;
use App\User;
use Tests\TestCase;

class AuthorSaleDisableTest extends TestCase
{
    public function testSalesDisableHttp()
    {
        $author = Author::factory()
            ->with_author_manager_can_sell()
            ->with_book_for_sale()
            ->create();

        $manager = $author->managers()->first();
        $book = $author->books()->first();
        $seller = $manager->user;
        $book->create_user_id = $seller->id;
        $book->save();
        $book->refresh();

        $user = User::factory()->create();

        $this->assertFalse($user->can('read', $book));
        $this->assertTrue($user->can('buy', $book));
        $this->assertTrue($seller->can('sell', $book));

        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('authors.sales.disable', $author))
            ->assertRedirect(route('authors.show', $author))
            ->assertSessionHas(['success' => __('manager.ability_to_sell_books_for_the_author_is_disabled')]);

        $manager->refresh();
        $book->refresh();

        $this->assertFalse($user->can('buy', $book));
        $this->assertFalse($seller->can('sell', $book));
        $this->assertFalse($manager->can_sale);
        $this->assertEquals(0, $book->price);
        $this->assertFalse($user->can('read', $book));
    }
}
