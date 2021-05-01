<?php

namespace Tests\Feature\User\Delete;

use App\User;
use Tests\TestCase;

class UserRestoreTest extends TestCase
{
    public function testRestore()
    {
        config(['activitylog.enabled' => true]);

        $admin = User::factory()->admin()->create();

        $user = User::factory()->deleted()->create();

        $this->actingAs($admin)
            ->get(route('users.restore', ['user' => $user]))
            ->assertRedirect();

        $user->refresh();

        $this->assertFalse($user->trashed());

        $this->assertEquals(1, $user->activities()->count());

        $activity = $user->activities()->first();

        $this->assertNotNull($activity);
        $this->assertEquals($activity->subject_id, $user->id);
        $this->assertEquals($activity->subject_type, 'user');
        $this->assertEquals($activity->causer_id, $admin->id);
        $this->assertEquals($activity->causer_type, 'user');
        $this->assertEquals($activity->description, 'restored');
    }
}
