<?php

namespace Tests\Feature\Artisan;

use App\Manager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DetachInactiveEditorsTest extends TestCase
{
	public function testDontDeleteIfNotEditor()
	{
		$manager = factory(Manager::class)
			->states('character_author', 'accepted')
			->create();

		$user = $manager->user;

		Carbon::setTestNow(now()->addMonths(13));

		Artisan::call('managers:delete_inactive_editors', ['months_have_passed_since_the_last_visit' => 12, 'latest_id' => $manager->id]);

		$manager->refresh();

		$this->assertFalse($manager->trashed());
	}

	public function testDeleteIfTimePassed()
	{
		$manager = factory(Manager::class)
			->states('character_editor', 'accepted')
			->create();

		$user = $manager->user;

		Carbon::setTestNow(now()->addMonths(13));

		Artisan::call('managers:delete_inactive_editors', ['months_have_passed_since_the_last_visit' => 12, 'latest_id' => $manager->id]);

		$manager->refresh();

		$this->assertTrue($manager->trashed());
	}

	public function testDontDeleteIfTimeNotPassed()
	{
		$manager = factory(Manager::class)
			->states('character_editor', 'accepted')
			->create();

		$user = $manager->user;

		Carbon::setTestNow(now()->addMonths(11));

		Artisan::call('managers:delete_inactive_editors', ['months_have_passed_since_the_last_visit' => 12, 'latest_id' => $manager->id]);

		$manager->refresh();

		$this->assertFalse($manager->trashed());
	}
}
