<?php

namespace Tests\Feature\User;

use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserSeeAdsPolicyTest extends TestCase
{
    public function testSeeAds()
    {
        $user = User::factory()->create();
        $user->group->not_show_ad = false;
        $user->push();

        $this->assertTrue($user->can('see_ads', User::class));

        $user->group->not_show_ad = true;
        $user->push();

        $this->assertFalse($user->can('see_ads', User::class));

        $this->assertTrue((new User())->can('see_ads', User::class));
    }

    public function testDontShowAdsIfCreatedBooksMoreThan()
    {
        $user = User::factory()->create();
        $user->group->not_show_ad = false;
        $user->data->created_books_count = 9;
        $user->push();
        $user->refresh();

        $this->assertTrue($user->can('see_ads', User::class));

        $user->data->created_books_count = 10;
        $user->push();
        $user->refresh();

        $this->assertFalse($user->can('see_ads', User::class));

        $user->data->created_books_count = 11;
        $user->push();
        $user->refresh();

        $this->assertFalse($user->can('see_ads', User::class));
    }

    public function testShowIfAdsDisabledTimeIsPast()
    {
        $user = User::factory()->create();
        $user->group->not_show_ad = false;
        $user->data->ads_disabled_until = Carbon::now();
        $user->push();
        $user->refresh();

        $this->travel(1)->days();

        $this->assertTrue($user->can('see_ads', User::class));
    }

    public function testDontShowIfAdsDisabledTimeIsFuture()
    {
        $this->travel(1)->days();

        $user = User::factory()->create();
        $user->group->not_show_ad = false;
        $user->data->ads_disabled_until = Carbon::now();
        $user->push();
        $user->refresh();

        $this->travelBack();

        $this->assertFalse($user->can('see_ads', User::class));
    }

    public function testShowAdsIfAdsDisabledUntilIsNull()
    {
        $user = User::factory()->create();
        $user->group->not_show_ad = false;
        $user->data->ads_disabled_until = null;
        $user->push();
        $user->refresh();

        $this->assertTrue($user->can('see_ads', User::class));
    }
}
