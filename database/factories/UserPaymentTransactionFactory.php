<?php

namespace Database\Factories;

use App\Enums\PaymentStatusEnum;
use App\User;
use App\UserIncomingPayment;
use App\UserMoneyTransfer;
use App\UserOutgoingPayment;
use App\UserPaymentTransaction;

class UserPaymentTransactionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserPaymentTransaction::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $sum = rand(-100, 100);

        return [
            'user_id' => User::factory(),
            'sum' => $sum,
            'type' => 0,
            'params' => [],
            'status' => PaymentStatusEnum::Success,
            'status_changed_at' => now()
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->user->balance(true);
            $item->user->frozen_balance(true);
        });
    }

    public function incoming()
    {
        return $this->afterMaking(function ($item) {
            $payment = UserIncomingPayment::factory()->create(['user_id' => $item->user_id]);

            $item->operable_type = 14;
            $item->operable_id = $payment->id;
            $item->typeDeposit();
        })->afterCreating(function ($item) {

        });
    }

    public function outgoing()
    {
        return $this->afterMaking(function ($item) {
            $payment = UserOutgoingPayment::factory()->create(['user_id' => $item->user_id]);

            $item->operable_type = 15;
            $item->operable_id = $payment->id;
            $item->typeWithdrawal();
            $item->sum = -abs($item->sum);
        })->afterCreating(function ($item) {

        });
    }

    public function success()
    {
        return $this->afterMaking(function ($item) {
            $item->statusSuccess();
        })->afterCreating(function ($item) {

        });
    }

    public function wait()
    {
        return $this->afterMaking(function ($item) {
            $item->statusWait();
        })->afterCreating(function ($item) {

        });
    }

    public function processing()
    {
        return $this->afterMaking(function ($item) {
            $item->statusProcessing();
        })->afterCreating(function ($item) {

        });
    }

    public function canceled()
    {
        return $this->afterMaking(function ($item) {
            $item->statusCanceled();
        })->afterCreating(function ($item) {

        });
    }

    public function error()
    {
        return $this->afterMaking(function ($item) {
            $item->statusError();
        })->afterCreating(function ($item) {

        });
    }

    public function unitpay()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            if ($item->isDeposit()) {

                if ($item->isStatusSuccess()) {
                    $payment_id = rand(1000, 100000);

                    $item->operable->params = [
                        'result' => [
                            'paymentId' => $payment_id,
                            'projectId' => config('unitpay.project_id'),
                            'account' => $item->id,
                            'payerSum' => $item->sum + 10,
                            'payerCurrency' => 'RUB',
                            'profit' => $item->sum,
                            'paymentType' => $item->operable->payment_type,
                            'orderSum' => $item->sum,
                            'orderCurrency' => 'RUB',
                            'date' => now()->toDateTimeString(),
                            'purse' => '1234123412341234',
                            'test' => '0'
                        ]
                    ];
                    $item->operable->save();
                }

                if ($item->isStatusProcessing()) {
                    $payment_id = rand(1000, 100000);

                    $item->operable->params = [
                        'result' => [
                            'paymentId' => $payment_id,
                            'projectId' => config('unitpay.project_id'),
                            'account' => $item->id,
                            'payerSum' => $item->sum + 10,
                            'payerCurrency' => 'RUB',
                            'profit' => $item->sum,
                            'paymentType' => $item->operable->payment_type,
                            'orderSum' => $item->sum,
                            'orderCurrency' => 'RUB',
                            'date' => now()->toDateTimeString()
                        ]
                    ];
                }

                if ($item->isStatusError()) {
                    $payment_id = rand(1000, 100000);

                    $item->operable->params = [
                        'result' => [
                            'paymentId' => $payment_id,
                            'projectId' => config('unitpay.project_id'),
                            'account' => $item->id,
                            'payerSum' => $item->sum + 10,
                            'payerCurrency' => 'RUB',
                            'profit' => $item->sum,
                            'paymentType' => $item->operable->payment_type,
                            'orderSum' => $item->sum,
                            'orderCurrency' => 'RUB',
                            'date' => now()->toDateTimeString()
                        ]
                    ];
                }

                if (!empty($item->operable->getParamsArray()['result']['paymentId'])) {
                    $item->operable->payment_id = $item->operable->getParamsArray()['result']['paymentId'];
                }

                $item->sum = abs($item->sum);
            }

            if ($item->isWithdrawal()) {
                $item->sum = -abs($item->sum);

                if ($item->isStatusSuccess()) {
                    $payment_id = rand(1000, 100000);

                    $item->operable->params = [
                        'result' => [
                            'message' => 'Выплата произведена',
                            'payoutId' => rand(10000, 1000000000),
                            'status' => 'success',
                            'partnerBalance' => '1000.10',
                            'payoutCommission' => '0.45',
                            "partnerCommission" => "0.00",
                            'sum' => $item->sum,
                            'createDate' => '2019-08-26 13:30:54',
                            'completeDate' => '2019-08-26 13:30:55',
                            'transactionId' => uniqid()
                        ]
                    ];
                    $item->operable->save();
                } elseif ($item->isStatusProcessing()) {
                    $payment_id = rand(1000, 100000);

                    $item->operable->params = [
                        'result' => [
                            'message' => 'Выплата произведена',
                            'payoutId' => rand(10000, 1000000000),
                            'status' => 'not_completed',
                            'partnerBalance' => '1000.10',
                            'payoutCommission' => '0.45',
                            "partnerCommission" => "0.00",
                            'sum' => $item->sum,
                            'createDate' => '2019-08-26 13:30:54',
                            'completeDate' => '2019-08-26 13:30:55',
                            'transactionId' => uniqid()
                        ]
                    ];
                    $item->operable->save();
                }
            }

            $item->operable->save();
            $item->save();
        });
    }

    public function transfer()
    {
        return $this->afterMaking(function ($item) {

            $transfer = UserMoneyTransfer::factory()->create(['sender_user_id' => $item->user_id]);

            $item->sum = -abs($item->sum);
            $item->operable_type = 17;
            $item->operable_id = $transfer->id;
            $item->typeTransfer();

        })->afterCreating(function ($item) {

        });
    }

    public function receipt()
    {
        return $this->afterMaking(function ($item) {

            $transfer = UserMoneyTransfer::factory()->create(['recepient_user_id' => $item->user_id]);

            $item->sum = abs($item->sum);
            $item->operable_type = 17;
            $item->operable_id = $transfer->id;
            $item->typeReceipt();

        })->afterCreating(function ($item) {

        });
    }
}
