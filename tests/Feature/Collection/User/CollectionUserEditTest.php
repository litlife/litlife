<?php

namespace Tests\Feature\Collection\User;

use App\CollectionUser;
use Illuminate\Support\Str;
use Tests\TestCase;

class CollectionUserEditTest extends TestCase
{
	public function testEditHttp()
	{
		$collectionUser = CollectionUser::factory()->create(['can_user_manage' => true]);

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$this->actingAs($user)
			->get(route('collections.users.edit', ['collection' => $collection, 'user' => $user]))
			->assertOk();
	}

	public function testUpdateHttp()
	{
		$collectionUser = CollectionUser::factory()->create();

		$user = $collectionUser->user;
		$collection = $collectionUser->collection;

		$description = Str::random(8);

		$this->actingAs($collection->create_user)
			->patch(route('collections.users.update', ['collection' => $collection, 'user' => $user]), [
				'description' => $description,
				'can_edit' => true,
				'can_add_books' => false,
				'can_remove_books' => true,
				'can_edit_books_description' => false,
				'can_comment' => true,
				'can_user_manage' => true
			])
			->assertSessionHasNoErrors()
			->assertRedirect(route('collections.users.index', $collection))
			->assertSessionHas(['success' => __('collection_user.user_data_is_saved')]);

		$collectionUser->refresh();

		$this->assertEquals($collection->id, $collectionUser->collection_id);
		$this->assertEquals($user->id, $collectionUser->user_id);
		$this->assertTrue($collectionUser->can_edit);
		$this->assertFalse($collectionUser->can_add_books);
		$this->assertTrue($collectionUser->can_remove_books);
		$this->assertFalse($collectionUser->can_edit_books_description);
		$this->assertTrue($collectionUser->can_comment);
		$this->assertTrue($collectionUser->can_user_manage);
		$this->assertEquals($description, $collectionUser->description);
	}

}
