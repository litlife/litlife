<?php

namespace Tests\Feature\Collection\Book;

use App\CollectedBook;
use App\Collection;
use App\Enums\StatusEnum;
use App\User;
use Tests\TestCase;

class CollectionBookIndexTest extends TestCase
{
	public function testBooksHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$collected_book = factory(CollectedBook::class)
			->create();

		$collection = $collected_book->collection;
		$collection->status = StatusEnum::Accepted;
		$collection->who_can_add = 'everyone';
		$collection->save();
		$collection->refresh();

		$book = $collected_book->book;

		$response = $this->actingAs($user)
			->get(route('collections.books', $collection))
			->assertOk();
		//->assertSeeText($book->title);

		$resource = $response->original->gatherData()['resource'];

		$this->assertFalse($resource->isSaveSetting());
		$this->assertEquals('gallery', $resource->getDefaultInputValue('view'));
		$this->assertEquals('gallery', $resource->getInputValue('view'));
	}

	public function testBooksOkIfUserGuestCanSeeEveryone()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$this->get(route('collections.books', $collection))
			->assertOk();
	}

	public function testBooksForbiddenIfUserGuestCanSeeMe()
	{
		$collection = factory(Collection::class)
			->states('private')
			->create();

		$this->get(route('collections.books', $collection))
			->assertForbidden();
	}

	public function testOrderIsOk()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create();

		$collectedBook = factory(CollectedBook::class)
			->create([
				'collection_id' => $collection->id,
				'number' => 2,
				'comment' => 'test'
			]);

		$response = $this->get(route('collections.books', [
			'collection' => $collection,
			'order' => 'collection_number_asc'
		]))->assertOk();

		$response = $this->get(route('collections.books', [
			'collection' => $collection,
			'order' => 'collection_number_desc'
		]))->assertOk();

		$response = $this->get(route('collections.books', [
			'collection' => $collection,
			'order' => 'oldest_added_to_collection'
		]))->assertOk();

		$response = $this->get(route('collections.books', [
			'collection' => $collection,
			'order' => 'latest_added_to_collection'
		]))->assertOk();
	}
}
