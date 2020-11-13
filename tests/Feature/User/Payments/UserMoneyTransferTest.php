<?php

namespace Tests\Feature\User\Payments;

use App\Jobs\MoneyTransferJob;
use App\User;
use Exception;
use Tests\TestCase;

class UserMoneyTransferTest extends TestCase
{
    public function testPolicy()
    {
        $user = User::factory()->create();

        $this->assertFalse($user->can('transfer_money', User::class));

        $user->group->transfer_money = true;
        $user->push();
        $user->refresh();

        $this->assertTrue($user->can('transfer_money', User::class));
    }

    public function testJob()
    {
        $sender = User::factory()
            ->withMoneyOnBalance()
            ->create();

        $recepient = User::factory()->create();

        $this->assertEquals(1000, $sender->balance(true));
        $this->assertEquals(0, $recepient->balance(true));

        $sum = rand(100, 900);

        dispatch(new MoneyTransferJob($sender, $recepient, $sum));

        $this->assertEquals(1000 - $sum, $sender->fresh()->balance());
        $this->assertEquals($sum, $recepient->fresh()->balance());

        $sender_transaction = $sender->transfers()->first()->sender_transaction;

        $this->assertEquals($sender->id, $sender_transaction->user_id);
        $this->assertEquals(-$sum, $sender_transaction->sum);
        $this->assertTrue($sender_transaction->isTransfer());
        $this->assertTrue($sender_transaction->isStatusSuccess());
        $this->assertEquals(1000, $sender_transaction->balance_before);
        $this->assertEquals(1000 - $sum, $sender_transaction->balance_after);

        $recepient_transaction = $recepient->receiving()->first()->recepient_transaction;

        $this->assertEquals($recepient->id, $recepient_transaction->user_id);
        $this->assertEquals($sum, $recepient_transaction->sum);
        $this->assertTrue($recepient_transaction->isReceipt());
        $this->assertTrue($recepient_transaction->isStatusSuccess());
        $this->assertEquals(0, $recepient_transaction->balance_before);
        $this->assertEquals($sum, $recepient_transaction->balance_after);
    }

    public function testNotEnoughMoneyException()
    {
        $sender = User::factory()
            ->withMoneyOnBalance(1000)
            ->create();
        $sender->balance = 2000;
        $sender->save();
        $sender->refresh();

        $recepient = User::factory()->create();

        $this->assertEquals(1000, $sender->balance(true));
        $this->assertEquals(0, $recepient->balance(true));

        $sum = 1001;

        $this->expectException(Exception::class);

        dispatch(new MoneyTransferJob($sender, $recepient, $sum));

        $this->assertEquals(1000, $sender->fresh()->balance(true));
        $this->assertEquals(0, $recepient->fresh()->balance(true));
    }

    public function testOrderTransferHttpIsOk()
    {
        $sender = User::factory()->withMoneyOnBalance()->create();
        $sender->group->transfer_money = true;
        $sender->push();

        $this->actingAs($sender)
            ->get(route('users.wallet.transfer', ['user' => $sender]))
            ->assertOk();
    }

    public function testTransferHttp()
    {
        $sender = User::factory()->withMoneyOnBalance()->create();
        $sender->group->transfer_money = true;
        $sender->push();

        $recepient = User::factory()->create();

        $sum = rand(100, 900);

        $this->assertEquals(1000, $sender->balance(true));
        $this->assertEquals(0, $recepient->balance(true));

        $this->actingAs($sender)
            ->post(route('users.wallet.transfer.save', ['user' => $sender]), [
                'recepient_id' => $recepient->id,
                'sum' => $sum
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('users.wallet', ['user' => $sender]))
            ->assertSessionHas(['success' => __('user_money_transfer.transfer_to_user_success', ['sum' => $sum, 'user_name' => $recepient->userName])]);

        $this->assertEquals(1000 - $sum, $sender->fresh()->balance());
        $this->assertEquals($sum, $recepient->fresh()->balance());
    }

    public function testCantTransferToSelf()
    {
        $sender = User::factory()->withMoneyOnBalance()->create();
        $sender->group->transfer_money = true;
        $sender->push();

        $response = $this->actingAs($sender)
            ->post(route('users.wallet.transfer.save', ['user' => $sender]), [
                'sum' => '123',
                'recepient_id' => $sender->id,
            ])
            ->assertRedirect();

        $response->assertSessionHasErrors(['recepient_id' => __('user_money_transfer.the_recipient_id_must_not_match_the_sender_id')]);
    }
}
