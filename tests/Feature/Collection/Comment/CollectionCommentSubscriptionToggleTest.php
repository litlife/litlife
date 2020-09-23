<?php

namespace Tests\Feature\Collection\Comment;

use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionCommentSubscriptionToggleTest extends TestCase
{
	public function testToggleWithoutAjax()
	{
		$collection = factory(Collection::class)
			->states('accepted')
			->create()
			->fresh();

		$admin = factory(User::class)
			->create();

		$response = $this->actingAs($admin)
			->followingRedirects()
			->get(route('collections.event_notification_subcriptions.toggle', $collection))
			->assertOk()
			->assertSeeText(__('collection.notifications_for_new_collection_comments_has_been_successfully_enabled', ['collection_title' => $collection->title]));

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('collections.event_notification_subcriptions.toggle', $collection))
			->assertOk()
			->assertSeeText(__('collection.notifications_about_new_comments_to_the_collection_successfully_disabled', ['collection_title' => $collection->title]));
	}
}
