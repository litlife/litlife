<?php

namespace Tests\Feature\Collection;

use App\Collection;
use Illuminate\Support\Str;
use Tests\TestCase;

class CollectionIndexTest extends TestCase
{
	public function testIndexHttp()
	{
		$collection = factory(Collection::class)
			->create();

		$user = $collection->create_user;

		$this->actingAs($user)
			->get(route('collections.index'))
			->assertOk();
	}

	public function testSearchTitle()
	{
		$title = Str::random(5);
		$description = Str::random(5);

		$collection = factory(Collection::class)
			->create([
				'title' => $title,
				'description' => $description
			]);

		$this->actingAs($collection->create_user)
			->get(route('collections.index', [
				'search' => $title
			]))
			->assertOk()
			->assertDontSeeText(__('collection.nothing_found'))
			->assertSeeText($title)
			->assertSeeText($description);
	}

	public function testPerPage()
	{
		$response = $this->get(route('collections.index', ['per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['collections']->perPage());

		$response = $this->get(route('collections.index', ['per_page' => 200]))
			->assertOk();

		$this->assertEquals(100, $response->original->gatherData()['collections']->perPage());
	}

	public function testSeeCollectionForEveryoneInList()
	{
		$title = Str::random(8);

		$collection = factory(Collection::class)
			->states('accepted')
			->create([
				'title' => $title
			]);

		$this->get(route('collections.index', ['title' => $title]))
			->assertOk()
			->assertSeeText($title);
	}

	public function testDontSeePrivateCollectionInList()
	{
		$title = Str::random(8);

		$collection = factory(Collection::class)
			->states('private')
			->create([
				'title' => $title
			]);

		$this->get(route('collections.index', ['title' => $title]))
			->assertOk()
			->assertDontSeeText($title);
	}
}
