<?php

namespace Tests\Feature\Collection;

use App\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;

class CollectionShowTest extends TestCase
{
	public function testShowViewCount()
	{
		$collection = factory(Collection::class)
			->create(['views_count' => 5]);

		$this->actingAs($collection->create_user)
			->get(route('collections.show', ['collection' => $collection]))
			->assertOk();

		$collection->refresh();

		$this->assertEquals(6, $collection->views_count);
	}

	public function testShowOkIfUserGuestCanSeeEveryone()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$this->get(route('collections.show', $collection))
			->assertOk();
	}

	public function testShowForbiddenIfUserGuestCanSeeMe()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->get(route('collections.show', $collection))
			->assertForbidden();
	}

	public function testCollectionNotInteger()
	{
		$this->get(route('collections.show', ['collection' => Str::random(8)]))
			->assertNotFound();
	}
}
