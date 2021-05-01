<?php

namespace Database\Factories;

use App\AchievementUser;
use App\Enums\Gender;
use App\ReferredUser;
use App\User;
use App\UserAuthLog;
use App\UserEmail;
use App\UserGroup;
use App\UserPaymentDetail;
use App\UserPaymentTransaction;
use App\UserPhoto;
use App\UserPurchase;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        static $password;

        return [
            'nick' => uniqid(),
            'last_name' => preg_replace('/(\'|\"|ё)/iu', '', $this->faker->lastName),
            'first_name' => preg_replace('/(\'|\"|ё)/iu', '', $this->faker->firstName),
            'email' => $this->faker->unique()->safeEmail,
            'password' => '' ?: $password = md0('password'),
            'gender' => Gender::male,
            'reg_ip' => $this->faker->ipv4,
            'born_date' => $this->faker->date('Y-m-d', '-20 years'),
            'city' => $this->faker->city
        ];
    }

    public function configure()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $group = UserGroup::factory()->user()->create();

            $item->groups()->attach($group);

            $item->load('groups');
        });
    }

    public function with_user_group()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $group = UserGroup::factory()->user()->create();

            $item->groups()->sync($group);

            $item->load('groups');
        });
    }

    public function with_user_permissions()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->group->manage_collections = true;
            $item->group->blog = true;
            $item->group->display_technical_information = false;
            $item->group->admin_comment = false;
            $item->group->author_editor_request = true;
            $item->group->save();
        });
    }

    public function without_email()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->emails()->delete();
            $item->refreshConfirmedMailboxCount();
            $item->save();
        });
    }

    public function with_not_confirmed_email()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            foreach ($item->emails as $email) {
                $email->delete();
            }

            $email = UserEmail::factory()
                ->not_confirmed()
                ->create([
                    'user_id' => $item->id
                ]);
        });
    }

    public function with_auth_log()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {

            $auth_log = UserAuthLog::factory()
                ->create(['user_id' => $item->id]);

        });
    }

    public function administrator()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->groups()->detach();

            $group = UserGroup::factory()
                ->administrator()
                ->create();

            $item->groups()->attach($group);
            $item->load('groups');
        });
    }

    public function admin()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $item->groups()->detach();

            $group = UserGroup::factory()
                ->administrator()
                ->create();

            $item->groups()->attach($group);
            $item->load('groups');
        });
    }

    public function withMoneyOnBalance($amount = 1000)
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) use ($amount) {
            $payment = UserPaymentTransaction::factory()
                ->incoming()
                ->create(['sum' => $amount, 'user_id' => $item->id]);

            $item->balance(true);
        });
    }

    public function withSelledBook($bookPrice = 1000)
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) use ($bookPrice) {
            $payment = UserPurchase::factory()
                ->book()
                ->create(['price' => $bookPrice, 'seller_user_id' => $item->id, 'site_commission' => 0]);

            $item->balance(true);
        });
    }

    public function with_wallet()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $wallet = UserPaymentDetail::factory()
                ->create(['user_id' => $item->id])
                ->fresh();
        });
    }

    public function with_purchased_book()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $purchase = UserPurchase::factory()
                ->book()
                ->create(['buyer_user_id' => $item->id]);
        });
    }

    public function referred()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $referred_by_user = User::factory()->with_confirmed_email()->create();

            $referrer = ReferredUser::factory()
                ->create([
                    'referred_by_user_id' => $referred_by_user->id,
                    'referred_user_id' => $item->id
                ]);

            $referred_by_user->refer_users_refresh();
            $referred_by_user->save();
            $referred_by_user->refresh();
        });
    }

    public function with_confirmed_email()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {
            $email = UserEmail::factory()
                ->create([
                    'user_id' => $item->id,
                    'notice' => true,
                    'rescue' => true,
                    'show_in_profile' => true,
                    'confirm' => true
                ]);
        });
    }

    public function with_avatar()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {

            $photo = UserPhoto::factory()
                ->create(['user_id' => $item->id]);

            $item->avatar_id = $photo->id;
            $item->save();
        });
    }

    public function suspended()
    {
        return $this->afterMaking(function ($item) {
            $item->suspend();
        })->afterCreating(function ($item) {

        });
    }

    public function with_achievement()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {

            $achievementUser = AchievementUser::factory()
                ->create(['user_id' => $item->id]);

        });
    }

    public function male()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {

        })->state(function (array $attributes) {
            return [
                'gender' => 'male'
            ];
        });
    }

    public function female()
    {
        return $this->afterMaking(function ($item) {

        })->afterCreating(function ($item) {

        })->state(function (array $attributes) {
            return [
                'gender' => 'female'
            ];
        });
    }

    public function deleted()
    {
        return $this->state(function (array $attributes) {
            return [
                'deleted_at' => now()
            ];
        });
    }
}
