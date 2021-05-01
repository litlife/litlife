<?php

namespace Tests\Feature\Artisan;

use App\BookVote;
use App\User;
use Tests\TestCase;

class DeleteBookRatingIfUserDeletedTest extends TestCase
{
    public function testDeleteRateIfUserHasBookRateCountMoreThanZeroAndDaysPassed()
    {
        $rating = BookVote::factory()
            ->for(User::factory()->state([
                'deleted_at' => now()->subDays(8)
            ]), 'create_user')
            ->create();

        $user = $rating->create_user()->onlyTrashed()->first();

        $this->assertNotNull($user);

        $this->assertTrue($user->trashed());

        $this->artisan('user:if_deleted_delete_ratings', ['days_passed' => 7, 'user_id' => $user->id])
            ->assertExitCode(0);

        $rating->refresh();

        $this->assertTrue($rating->trashed());
    }

    public function testDontDeleteRateIfDaysNotPassed()
    {
        $rating = BookVote::factory()
            ->for(User::factory()->state([
                'deleted_at' => now()->subDays(5)
            ]), 'create_user')
            ->create();

        $user = $rating->create_user()->onlyTrashed()->first();

        $this->artisan('user:if_deleted_delete_ratings', ['days_passed' => 7, 'user_id' => $user->id])
            ->assertExitCode(0);

        $rating->refresh();

        $this->assertFalse($rating->trashed());
    }

    public function testDontDeleteRateIfUserNotDeleted()
    {
        $rating = BookVote::factory()
            ->create();

        $user = $rating->create_user()->first();

        $this->artisan('user:if_deleted_delete_ratings', ['days_passed' => 7, 'user_id' => $user->id])
            ->assertExitCode(0);

        $rating->refresh();

        $this->assertFalse($rating->trashed());
    }
}
