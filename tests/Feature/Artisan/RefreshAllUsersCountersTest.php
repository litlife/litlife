<?php

namespace Tests\Feature\Artisan;

use App\User;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RefreshAllUsersCountersTest extends TestCase
{
    public function testCommand()
    {
        $user = User::factory()->create(['updated_at' => now()->subYear()]);

        Artisan::call('refresh:all_users_counters', ['latest_id' => $user->id]);

        $user->refresh();

        $this->assertGreaterThanOrEqual($user->updated_at->timestamp, now()->timestamp);
    }
}
