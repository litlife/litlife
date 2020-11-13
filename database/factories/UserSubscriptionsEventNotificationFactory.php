<?php

namespace Database\Factories;

use App\Collection;
use App\Enums\UserSubscriptionsEventNotificationType;
use App\User;
use App\UserSubscriptionsEventNotification;
use Illuminate\Database\Eloquent\Relations\Relation;

class UserSubscriptionsEventNotificationFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserSubscriptionsEventNotification::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'notifiable_user_id' => User::factory()
        ];
    }

    public function collection()
    {
        return $this->afterMaking(function ($item) {
            $collection = Collection::factory()->create();

            foreach (Relation::morphMap() as $alias => $model) {
                if ($model == 'App\Collection') {
                    break;
                }
            }

            $item->eventable_type = $alias;
            $item->eventable_id = $collection->id;
        })->afterCreating(function ($item) {

        });
    }

    public function new_comment()
    {
        return $this->afterMaking(function ($item) {
            $item->event_type = UserSubscriptionsEventNotificationType::NewComment;
        })->afterCreating(function ($item) {

        });
    }
}
