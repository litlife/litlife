<?php

namespace Database\Factories;

use App\User;
use App\UserOutgoingPayment;
use App\UserPaymentDetail;
use App\UserPaymentTransaction;

class UserOutgoingPaymentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserOutgoingPayment::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $item_types = config('unitpay.allowed_outgoing_payment_types');

        return [
            'user_id' => User::factory(),
            'ip' => $this->faker->ipv4,
            'purse' => $this->faker->creditCardNumber,
            'payment_type' => $item_types[array_rand($item_types)],
            'wallet_id' => function (array $item) {
                return UserPaymentDetail::factory()
                    ->create([
                        'user_id' => $item['user_id'],
                        'type' => $item['payment_type'],
                        'number' => $item['purse']
                    ])->fresh()->id;
            },
            'payment_aggregator' => 'unitpay',
            'payment_aggregator_transaction_id' => null,
            'params' => null
        ];
    }

    public function success()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->payment_aggregator_transaction_id = $this->faker->randomNumber(5);
            $item->save();

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item->user_id,
                    'sum' => -rand(50, 100)
                ]);
            $transaction->typeWithdrawal();
            $transaction->statusSuccess();

            $item->transaction()->save($transaction);

            $item->user->balance(true);
        });
    }

    public function wait()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->payment_aggregator_transaction_id = null;
            $item->save();

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item->user_id,
                    'sum' => -rand(50, 100),
                ]);
            $transaction->typeWithdrawal();
            $transaction->statusWait();

            $item->transaction()->save($transaction);

            $item->user->balance(true);
        });
    }

    public function processing()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->payment_aggregator_transaction_id = $this->faker->randomNumber(5);
            $item->save();

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item->user_id,
                    'sum' => -rand(50, 100)
                ]);
            $transaction->typeWithdrawal();
            $transaction->statusProcessing();

            $item->transaction()->save($transaction);

            $item->user->balance(true);
        });
    }

    public function error()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->payment_aggregator_transaction_id = $this->faker->randomNumber(5);
            $item->params = [
                'error' => [
                    'message' => 'По вашему запросу ничего не нашлось.',
                    'code' => '100',
                ]
            ];
            $item->save();

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item->user_id,
                    'sum' => -rand(50, 100)
                ]);
            $transaction->typeWithdrawal();
            $transaction->statusError();

            $item->transaction()->save($transaction);

            $item->user->balance(true);
        });
    }

    public function canceled()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item->user_id,
                    'sum' => -rand(50, 100)
                ]);
            $transaction->typeWithdrawal();
            $transaction->statusCanceled();

            $item->transaction()->save($transaction);

            $item->user->balance(true);
        });
    }
}
