<?php

namespace Tests\Feature\Sequence;

use App\Sequence;
use App\User;
use Tests\TestCase;

class SequenceDeleteTest extends TestCase
{
	public function testDeleteHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = User::factory()->admin()->create();

		$sequence = Sequence::factory()->create();

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('sequences.delete', $sequence))
			->assertSeeText(__('sequence.deleted'));

		$sequence->refresh();

		$this->assertSoftDeleted($sequence);

		$this->assertEquals(1, $sequence->activities()->count());
		$activity = $sequence->activities()->first();
		$this->assertEquals('deleted', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testRestoreHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = User::factory()->admin()->create();

		$sequence = Sequence::factory()->create();
		$sequence->delete();

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('sequences.delete', $sequence))
			->assertDontSeeText(__('sequence.deleted'));

		$sequence->refresh();

		$this->assertFalse($sequence->trashed());

		$this->assertEquals(1, $sequence->activities()->count());
		$activity = $sequence->activities()->first();
		$this->assertEquals('restored', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}
}
