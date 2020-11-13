<?php

namespace Database\Factories;

use App\Book;
use App\User;
use App\UserPaymentTransaction;
use App\UserPurchase;

class UserPurchaseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserPurchase::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $price = rand(100, 150);

        $site_commission = rand(10, 30);

        return [
            'buyer_user_id' => User::factory(),
            'seller_user_id' => User::factory(),
            'price' => $price,
            'site_commission' => config('litlife.comission')
        ];
    }

    public function book()
    {
        return $this->afterMaking(function ($item) {
            $book = Book::factory()->with_section()->with_writer()->create();

            $item->purchasable_type = 'book';
            $item->purchasable_id = $book->id;
        })->afterCreating(function ($item) {

            $comission_sum = (($item->price / 100) * $item->site_commission);
            $seller_sum = $item->price - $comission_sum;

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item->buyer_user_id,
                    'sum' => -$item->price
                ]);
            $transaction->typeBuy();
            $transaction->statusSuccess();
            $item->transaction()->save($transaction);

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item->seller_user_id,
                    'sum' => $seller_sum
                ]);
            $transaction->typeSell();
            $transaction->statusSuccess();
            $item->transaction()->save($transaction);

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => 0,
                    'sum' => $comission_sum
                ]);
            $transaction->typeComission();
            $transaction->statusSuccess();
            $item->transaction()->save($transaction);
        });
    }

    public function with_seller_referer()
    {
        return $this->afterMaking(function ($item) {
            $item->seller_user_id = User::factory()->referred()->create()->id;
        })->afterCreating(function ($item) {

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item->seller->referred_by_user->first()->id,
                    'sum' => 2
                ]);
            $transaction->typeComissionRefererSeller();
            $transaction->statusSuccess();
            $item->transaction()->save($transaction);
        });
    }

    public function with_buyer_referer()
    {
        return $this->afterMaking(function ($item) {
            $item->buyer_user_id = User::factory()->referred()->create()
                ->id;
        })->afterCreating(function ($item) {

            $transaction = UserPaymentTransaction::factory()
                ->make([
                    'user_id' => $item->buyer->referred_by_user->first()->id,
                    'sum' => 3
                ]);

            $transaction->typeComissionRefererBuyer();
            $transaction->statusSuccess();
            $item->transaction()->save($transaction);
        });
    }

    public function canceled()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->cancel();
            $item->save();
        });
    }
}
