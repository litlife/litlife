<?php

namespace Tests\Feature\Collection;

use App\CollectedBook;
use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionTest extends TestCase
{
	public function testShareValue()
	{
		$collected_book = CollectedBook::factory()->create();

		$collection = $collected_book->collection;
		$book = $collected_book->book;

		$this->assertEquals(__('collection.collection') . ' "' . $collection->title . '" - ' . $collection->books_count . ' ' . mb_strtolower(trans_choice('collection.books', $collection->books_count)),
			$collection->getShareTitle());

		$this->assertEquals($collection->description, $collection->getShareDescription());
		$this->assertEquals(null, $collection->getShareImage());
		$this->assertEquals(route('collections.show', $collection), $collection->getShareUrl());
		$this->assertEquals(__('collection.share_a_collection'), $collection->getShareTooltip());
	}

	public function testCreateComplainReportHttpIsOk()
	{
		$user = User::factory()->admin()->create();

		$collection = Collection::factory()->create();

		$this->actingAs($user)
			->get(route('complains.report', ['type' => '18', 'id' => $collection->id]))
			->assertOk();
	}
}
