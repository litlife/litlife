<?php

namespace Tests\Feature\Listener;

use App\User;
use App\UserAuthLog;
use Illuminate\Auth\Events\Login;
use Tests\TestCase;

class LogSuccesfulLoginTest extends TestCase
{
    public function test()
    {
        $user = User::factory()->create();
        $user->last_activity_at = null;
        $user->save();
        $user->refresh();

        $this->assertNull($user->last_activity_at);

        event(new Login('web', $user, true));

        $user->refresh();

        $log = $user->auth_logs()->first();

        $this->assertNotNull($log);

        $this->assertEquals($log->user_id, $user->id);
        $this->assertEquals($log->ip, '127.0.0.1');
        $this->assertTrue($log->is_remember_me_enabled);

        $this->assertTrue($this->app['session']->get('show_greeting'));

        $this->assertNotNull($user->last_activity_at);
    }

    public function testDontWriteLogIfLatestSameUserAndTheTimeHasntPassedYet()
    {
        $user = User::factory()->create();

        $log = new UserAuthLog();
        $log->user_id = $user->id;
        $log->ip = request()->ip();
        $log->save();

        $this->assertEquals(1, $user->auth_logs()->count());

        event(new Login('web', $user, true));

        $this->assertEquals(1, $user->auth_logs()->count());
    }

    public function testWriteLogIfLatestSameUserAndTimePassed()
    {
        $user = User::factory()->create();

        $log = new UserAuthLog();
        $log->user_id = $user->id;
        $log->ip = request()->ip();
        $log->save();

        $this->assertEquals(1, $user->auth_logs()->count());

        $this->travel(20)->seconds();

        event(new Login('web', $user, true));

        $this->assertEquals(2, $user->auth_logs()->count());
    }
}
