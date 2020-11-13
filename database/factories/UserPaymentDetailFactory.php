<?php

namespace Database\Factories;

use App\User;
use App\UserPaymentDetail;

class UserPaymentDetailFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserPaymentDetail::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'type' => 'card',
            'number' => '4024007161972749'
        ];
    }

    public function card()
    {
        return $this->afterMaking(function ($item) {
            $item->type = 'card';
        })->afterCreating(function ($item) {

        });
    }

    public function ru_card()
    {
        return $this->afterMaking(function ($item) {
            $item->params = ["countryCode" => "RU"];
        })->afterCreating(function ($item) {

        });
    }

    public function mastercard()
    {
        return $this->afterMaking(function ($item) {
            $item->params = ["brand" => "MASTERCARD"];
        })->afterCreating(function ($item) {

        });
    }

    public function visa()
    {
        return $this->afterMaking(function ($item) {
            $item->params = ["brand" => "VISA"];
        })->afterCreating(function ($item) {

        });
    }

    public function webmoney()
    {
        return $this->afterMaking(function ($item) {
            $item->type = 'webmoney';
        })->afterCreating(function ($item) {

        });
    }

    public function qiwi()
    {
        return $this->afterMaking(function ($item) {
            $item->type = 'qiwi';
        })->afterCreating(function ($item) {

        });
    }

    public function yandex()
    {
        return $this->afterMaking(function ($item) {
            $item->type = 'yandex';
        })->afterCreating(function ($item) {

        });
    }
}
