<?php

namespace Tests\Feature\User;

use App\User;
use Tests\TestCase;

class UserDataAdsDisabledUntilAppendDaysTest extends TestCase
{
    public function testWhenNullValue()
    {
        $user = User::factory()->create();

        $this->assertNull($user->data->ads_disabled_until);

        $user->data->adsDisabledUntilAppendDays(42);
        $user->push();

        $this->assertNotNull($user->data->ads_disabled_until);
        $this->assertGreaterThan(now()->addDays(41), $user->data->ads_disabled_until);
        $this->assertLessThan(now()->addDays(43), $user->data->ads_disabled_until);

        $user->data->adsDisabledUntilAppendDays(42);
        $user->push();

        $this->assertGreaterThan(now()->addDays(83), $user->data->ads_disabled_until);
        $this->assertLessThan(now()->addDays(85), $user->data->ads_disabled_until);
    }

    public function testAppendFloatValue()
    {
        $user = User::factory()->create();

        $this->assertNull($user->data->ads_disabled_until);

        $user->data->adsDisabledUntilAppendDays(42.2);
        $user->push();

        $this->assertNotNull($user->data->ads_disabled_until);
        $this->assertGreaterThan(now()->addDays(41), $user->data->ads_disabled_until);
        $this->assertLessThan(now()->addDays(43), $user->data->ads_disabled_until);
    }
}
