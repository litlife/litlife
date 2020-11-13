<?php

namespace Database\Factories;

use App\User;
use App\UserIncomingPayment;
use App\UserPaymentTransaction;

class UserIncomingPaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserIncomingPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $item_types = ['mc', 'sms', 'card', 'webmoney', 'qiwi', 'paypal', 'liqpay', 'alfaClick', 'cash', 'applepay'];

        return [
            'payment_type' => $item_types[array_rand($item_types)],
            'user_id' => User::factory(),
            'ip' => $this->faker->ipv4,
            'currency' => 'RUB',
            'payment_aggregator' => 'unitpay',
            'params' => []
        ];
    }

    public function wait()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item['user_id'],
                    'sum' => rand(50, 100)
                ]);
            $transaction->typeDeposit();
            $transaction->statusWait();

            $item->transaction()->save($transaction);

            $item->user->balance(true);
        });
    }

    public function processing()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->payment_id = rand(100, 1000000);
            $item->save();

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item['user_id'],
                    'sum' => rand(50, 100)
                ]);

            $transaction->typeDeposit();
            $transaction->statusProcessing();
            $item->transaction()->save($transaction);
        });
    }

    public function unitpay_success()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->payment_id = rand(100, 1000000);
            $item->save();

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item['user_id'],
                    'sum' => rand(50, 100)
                ]);
            $transaction->typeDeposit();
            $transaction->statusSuccess();

            $item->transaction()->save($transaction);

            $item->params = [
                'result' => [
                    'unitpayId' => $item->payment_id,
                    'projectId' => config('unitpay.project_id'),
                    'account' => $item->id,
                    'payerSum' => $item->sum + 10,
                    'payerCurrency' => 'RUB',
                    'profit' => $item->sum,
                    'paymentType' => $item->payment_type,
                    'orderSum' => $item->sum,
                    'orderCurrency' => 'RUB',
                    'date' => now()->toDateTimeString(),
                    'purse' => '1234123412341234',
                    'test' => '0'
                ]
            ];
            $item->save();

            $item->user->balance(true);
        });
    }

    public function error()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->payment_id = rand(100, 1000000);
            $item->save();

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item['user_id'],
                    'sum' => rand(50, 100)
                ]);
            $transaction->typeDeposit();
            $transaction->statusError();

            $item->transaction()->save($transaction);
        });
    }

    public function canceled()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item['user_id'],
                    'sum' => rand(50, 100)
                ]);

            $transaction->typeDeposit();
            $transaction->statusCanceled();

            $item->transaction()->save($transaction);
        });
    }
}
