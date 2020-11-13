<?php

namespace Tests\Feature\User\Purchase;

use App\User;
use App\UserPurchase;
use Tests\TestCase;

class UserCancelPurchasePolicyTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCancelTrue()
    {
        $user = User::factory()->create();
        $user->group->view_financial_statistics = true;
        $user->push();

        $purchase = UserPurchase::factory()->book()->create();

        $this->assertTrue($user->can('cancel', $purchase));
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCancelFalseIfPurchaseCanceled()
    {
        $user = User::factory()->create();
        $user->group->view_financial_statistics = true;
        $user->push();

        $purchase = UserPurchase::factory()->book()->canceled()->create();

        $this->assertFalse($user->can('cancel', $purchase));
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCancelFalseIfCantViewFinStatistics()
    {
        $user = User::factory()->create();
        $user->group->view_financial_statistics = false;
        $user->push();

        $purchase = UserPurchase::factory()->book()->create();

        $this->assertFalse($user->can('cancel', $purchase));
    }
}
