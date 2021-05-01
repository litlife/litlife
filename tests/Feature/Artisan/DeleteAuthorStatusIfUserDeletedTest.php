<?php

namespace Tests\Feature\Artisan;

use App\AuthorStatus;
use App\User;
use Tests\TestCase;

class DeleteAuthorStatusIfUserDeletedTest extends TestCase
{
    public function testDeleteStatusIfUserHasAuthorStatusCountMoreThanZeroAndDaysPassed()
    {
        $status = AuthorStatus::factory()
            ->for(User::factory()->state([
                'deleted_at' => now()->subDays(8)
            ]), 'user')
            ->create();

        $user = $status->user()->onlyTrashed()->first();

        $this->assertNotNull($user);

        $this->assertTrue($user->trashed());

        $this->artisan('user:if_deleted_delete_author_statuses', ['days_passed' => 7, 'user_id' => $user->id])
            ->assertExitCode(0);

        $this->assertEquals(0, $user->author_read_statuses()->count());
    }

    public function testDontDeleteStatusIfDaysNotPassed()
    {
        $status = AuthorStatus::factory()
            ->for(User::factory()->state([
                'deleted_at' => now()->subDays(5)
            ]), 'user')
            ->create();

        $user = $status->user()->onlyTrashed()->first();

        $this->artisan('user:if_deleted_delete_author_statuses', ['days_passed' => 7, 'user_id' => $user->id])
            ->assertExitCode(0);

        $this->assertEquals(1, $user->author_read_statuses()->count());
    }
    
    public function testDeleteIfUserHasBookReadNowCountMoreThanZero()
    {
        $status = AuthorStatus::factory()
            ->for(User::factory()->state([
                'deleted_at' => now()->subDays(8)
            ]), 'user')
            ->create();

        $user = $status->user()->onlyTrashed()->first();

        $this->assertNotNull($user);

        $this->assertTrue($user->trashed());

        $this->artisan('user:if_deleted_delete_author_statuses', ['days_passed' => 7, 'user_id' => $user->id])
            ->assertExitCode(0);

        $this->assertEquals(0, $user->author_read_statuses()->count());
    }
}
