<?php

namespace Tests\Feature\Artisan;

use App\BookStatus;
use App\User;
use Tests\TestCase;

class DeleteBookStatusIfUserDeletedTest extends TestCase
{
    public function testDeleteStatusIfUserHasBookStatusCountMoreThanZeroAndDaysPassed()
    {
        $status = BookStatus::factory()
            ->for(User::factory()->state([
                'deleted_at' => now()->subDays(8)
            ]), 'user')
            ->create();

        $user = $status->user()->onlyTrashed()->first();

        $this->assertNotNull($user);

        $this->assertTrue($user->trashed());

        $this->artisan('user:if_deleted_delete_book_statuses', ['days_passed' => 7, 'user_id' => $user->id])
            ->assertExitCode(0);

        $this->assertEquals(0, $user->book_read_statuses()->count());
    }

    public function testDontDeleteStatusIfDaysNotPassed()
    {
        $status = BookStatus::factory()
            ->for(User::factory()->state([
                'deleted_at' => now()->subDays(5)
            ]), 'user')
            ->create();

        $user = $status->user()->onlyTrashed()->first();

        $this->artisan('user:if_deleted_delete_book_statuses', ['days_passed' => 7, 'user_id' => $user->id])
            ->assertExitCode(0);

        $this->assertEquals(1, $user->book_read_statuses()->count());
    }

    public function testDontDeleteStatusIfUserNotDeleted()
    {
        $status = BookStatus::factory()
            ->create();

        $user = $status->user;

        $this->artisan('user:if_deleted_delete_book_statuses', ['days_passed' => 7, 'user_id' => $user->id])
            ->assertExitCode(0);

        $this->assertEquals(1, $user->book_read_statuses()->count());
    }
}
