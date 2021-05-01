<?php

namespace Tests\Feature\User\Delete;

use App\User;
use Tests\TestCase;

class UserDeleteTest extends TestCase
{
    public function testDelete()
    {
        config(['activitylog.enabled' => true]);

        $admin = User::factory()->admin()->create();

        $user = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('users.delete', ['user' => $user]))
            ->assertRedirect();

        $user->refresh();

        $this->assertSoftDeleted($user);

        $this->assertEquals(1, $user->activities()->count());

        $activity = $user->activities()->latest()->first();

        $this->assertNotNull($activity);
        $this->assertEquals($activity->subject_id, $user->id);
        $this->assertEquals($activity->subject_type, 'user');
        $this->assertEquals($activity->causer_id, $admin->id);
        $this->assertEquals($activity->causer_type, 'user');
        $this->assertEquals($activity->description, 'deleted');
    }
}
