<?php

namespace Tests\Feature\Author;

use App\Author;
use App\AuthorSaleRequest;
use App\Manager;
use App\User;
use App\UserPaymentDetail;
use Tests\TestCase;

class AuthorHowToStartSellingBooksTest extends TestCase
{
    public function testIsNotAuthentificated()
    {
        $this->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.please_register_and_log_in'))
            ->assertDontSeeText(__('author_sale_request.you_register_and_log_in_to_the_site'))
            ->assertDontSeeText(__('author_sale_request.please_link_the_authors_page'))
            ->assertDontSeeText(__('author_sale_request.you_havent_sold_any_books_yet'))
            ->assertDontSeeText(__('author_sale_request.please_link_the_authors_page'))
            ->assertDontSeeText(__('author_sale_request.to_apply_for_sales_you_must_have_a_linked_author_page'));
    }

    public function testIsAuthentificated()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.you_register_and_log_in_to_the_site'))
            ->assertDontSeeText(__('author_sale_request.please_register_and_log_in'))
            ->assertSeeText(__('author_sale_request.to_apply_for_sales_you_must_have_a_linked_author_page'))
            ->assertDontSeeText(__('author_sale_request.now_you_can_sell_books'))
            ->assertDontSeeText(__('author_sale_request.you_havent_sold_any_books_yet'))
            ->assertDontSeeText(__('author_sale_request.you_dont_have_any_books_to_sell'));
    }

    public function testUserDontHaveConfirmedEmail()
    {
        $user = User::factory()->with_not_confirmed_email()->create();

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertDontSeeText(__('author_sale_request.you_have_a_confirmed_mailbox'))
            ->assertSeeText(__('author_sale_request.you_dont_have_any_confirmed_mailboxes'));
    }

    public function testUserHaveConfirmedEmail()
    {
        $user = User::factory()->with_confirmed_email()->create();

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.you_have_a_confirmed_mailbox'))
            ->assertDontSeeText(__('author_sale_request.you_dont_have_any_confirmed_mailboxes'));
    }

    public function testIsDontHaveManager()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.please_link_the_authors_page'));
    }

    public function testIsManagerOnReview()
    {
        $manager = Manager::factory()->character_author()->sent_for_review()->create();

        $user = $manager->user;

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.your_request_for_binding_is_on_review'));
    }

    public function testIsManagerRejected()
    {
        $manager = Manager::factory()->character_author()->rejected()->create();

        $user = $manager->user;

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.your_request_to_link_the_author_has_been_rejected'));
    }

    public function testIsManagerAccepted()
    {
        $manager = Manager::factory()->character_author()->accepted()->create();

        $user = $manager->user;

        $this->assertFalse($manager->can_sale);

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.you_have_successfully_attached_the_page_of_the_author'))
            ->assertDontSeeText(__('author_sale_request.to_apply_for_sales_you_must_have_a_linked_author_page'))
            ->assertDontSeeText(__('author_sale_request.please_link_the_authors_page'))
            ->assertSeeText(__('author_sale_request.you_have_not_yet_added_a_single_wallet_to_withdraw_funds'))
            ->assertDontSeeText(__('author_sale_request.you_have_a_wallet_for_withdrawal'));
    }

    public function testCanSellABookWithoutBooks()
    {
        $author = Author::factory()->with_author_manager_can_sell()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;

        $this->assertEquals(0, $author->written_books()->count());

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.you_register_and_log_in_to_the_site'))
            ->assertSeeText(__('author_sale_request.you_have_successfully_attached_the_page_of_the_author'))
            ->assertSeeText(__('author_sale_request.you_dont_have_any_books_to_sell'))
            ->assertDontSeeText(__('author_sale_request.now_you_can_apply_for_book_sales'))
            ->assertSeeText(__('author_sale_request.now_you_can_sell_books'));
    }

    public function testCanSellABookWithBookNotOnSale()
    {
        $author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;
        $book = $author->books()->first();

        $this->assertEquals(1, $author->written_books()->count());

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.you_register_and_log_in_to_the_site'))
            ->assertSeeText(__('author_sale_request.you_have_successfully_attached_the_page_of_the_author'))
            ->assertDontSeeText(__('author_sale_request.you_dont_have_any_books_to_sell'))
            ->assertDontSeeText(__('author_sale_request.now_you_can_apply_for_book_sales'))
            ->assertSeeText(__('author_sale_request.now_you_can_sell_books'))
            ->assertDontSeeText(__('author_sale_request.you_have_books_for_sale'))
            ->assertSeeText(__('author_sale_request.you_havent_sold_any_books_yet'));
    }

    public function testCanSellABookWithBookOnSale()
    {
        $author = Author::factory()->with_author_manager_can_sell()->with_book()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;
        $book = $author->books()->first();
        $book->price = 100;
        $book->save();

        $this->assertEquals(1, $author->written_books()->count());

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.you_register_and_log_in_to_the_site'))
            ->assertSeeText(__('author_sale_request.you_have_successfully_attached_the_page_of_the_author'))
            ->assertDontSeeText(__('author_sale_request.you_dont_have_any_books_to_sell'))
            ->assertDontSeeText(__('author_sale_request.now_you_can_apply_for_book_sales'))
            ->assertSeeText(__('author_sale_request.now_you_can_sell_books'))
            ->assertSeeText(__('author_sale_request.you_have_books_for_sale'));
    }

    public function testSaleRequestOnReview()
    {
        $sale_request = AuthorSaleRequest::factory()->sent_for_review()->create();

        $manager = $sale_request->manager;
        $author = $sale_request->author;
        $user = $manager->user;

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.your_request_for_book_sales_is_on_review'));
    }

    public function testSaleRequestRejected()
    {
        $sale_request = AuthorSaleRequest::factory()->rejected()->create();

        $manager = $sale_request->manager;
        $author = $sale_request->author;
        $user = $manager->user;

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.your_request_for_book_sales_has_been_rejected'));
    }

    public function testSaleRequestReviewStarts()
    {
        $sale_request = AuthorSaleRequest::factory()->review_starts()->create();

        $manager = $sale_request->manager;
        $author = $sale_request->author;
        $user = $manager->user;

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.your_request_for_book_sales_is_on_review'));
    }

    public function testHasNoWallet()
    {
        $author = Author::factory()->with_author_manager_can_sell()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;

        $this->assertEquals(0, $user->wallets->count());

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.you_have_not_yet_added_a_single_wallet_to_withdraw_funds'));
    }

    public function testHasWallet()
    {
        $author = Author::factory()->with_author_manager_can_sell()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;

        $wallet = UserPaymentDetail::factory()->create(['user_id' => $user->id]);

        $this->assertEquals(1, $user->wallets->count());

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.you_have_a_wallet_for_withdrawal'));
    }

    public function testSeeWalletOptionWhenLogined()
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.you_have_not_yet_added_a_single_wallet_to_withdraw_funds'))
            ->assertDontSee(__('author_sale_request.to_withdraw_funds_you_need_to_order_a_payment'));
    }

    public function testSeeOrderPayment()
    {
        $author = Author::factory()->with_author_manager_can_sell()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;
        $user->balance = config('litlife.min_outgoing_payment_sum') + 100;
        $user->save();

        $wallet = UserPaymentDetail::factory()->create(['user_id' => $user->id]);

        $this->assertEquals(1, $user->wallets->count());

        $this->actingAs($user)
            ->get(route('authors.how_to_start_selling_books'))
            ->assertOk()
            ->assertSeeText(__('author_sale_request.you_have_a_wallet_for_withdrawal'))
            ->assertSeeText(__('author_sale_request.order_payment'));
    }
}
