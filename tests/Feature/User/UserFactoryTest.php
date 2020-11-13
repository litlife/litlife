<?php

namespace Tests\Feature\User;

use App\User;
use Tests\TestCase;

class UserFactoryTest extends TestCase
{


    /**
     * A basic test example.
     *
     * @return void
     */

    public function testWithPurchasedBookState()
    {
        $user = User::factory()->with_purchased_book()->create();

        $purchase = $user->purchases->first();
        $book = $purchase->purchasable;

        $this->assertEquals($user->id, $user->purchases->first()->buyer_user_id);
        $this->assertNotNull($book);
    }

}
