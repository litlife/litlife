<?php

namespace Tests\Feature\Artisan;

use App\Manager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class DeleteManagersFromDeletedAuthorsTest extends TestCase
{
	public function testNotDeleteIfAuthorNotDeleted()
	{
		$manager = factory(Manager::class)->create();

		Artisan::call('managers:delete_if_author_deleted', [
			'latest_manager_id' => $manager->id
		]);

		$manager->refresh();

		$this->assertFalse($manager->trashed());
	}

	public function testNotDeleteIfDaysNotPassedSinceDeleted()
	{
		$manager = factory(Manager::class)->create();
		$manager->manageable->delete();
		$manager->refresh();
		$manager->load(['manageable' => function ($query) {
			$query->any();
		}]);

		Artisan::call('managers:delete_if_author_deleted', [
			'latest_manager_id' => $manager->id,
			'days_have_passed' => 31
		]);

		$manager->refresh();

		$this->assertFalse($manager->trashed());
	}

	public function testDeleteIfDaysNotPassedSinceDeleted()
	{
		$manager = factory(Manager::class)->create();
		$manager->manageable->delete();

		Carbon::setTestNow(now()->addDays(32));

		Artisan::call('managers:delete_if_author_deleted', [
			'latest_manager_id' => $manager->id,
			'days_have_passed' => 31
		]);

		$manager->refresh();

		$this->assertTrue($manager->trashed());
	}
}
