<?php

namespace Tests\Feature\Collection;

use App\CollectedBook;
use App\Collection;
use App\CollectionUser;
use App\Enums\StatusEnum;
use App\Enums\UserAccountPermissionValues;
use Illuminate\Support\Str;
use Tests\TestCase;

class CollectionEditTest extends TestCase
{
	public function testEditHttp()
	{
		$collection = Collection::factory()->create();

		$this->actingAs($collection->create_user)
			->get(route('collections.edit', $collection))
			->assertOk();
	}

	public function testUpdateHttp()
	{
		$collection = Collection::factory()->accepted()->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => StatusEnum::Private,
			'who_can_add' => 'me',
			'who_can_comment' => 'me',
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$this->actingAs($collection->create_user)
			->followingRedirects()
			->patch(route('collections.update', ['collection' => $collection]), $post)
			->assertOk()
			->assertSeeText(__('collection.data_successfully_updated'));

		$collection->refresh();

		$this->assertEquals($post['title'], $collection->title);
		$this->assertEquals($post['description'], $collection->description);
		$this->assertTrue($collection->isPrivate());
		$this->assertEquals(UserAccountPermissionValues::getValue($post['who_can_add']), $collection->who_can_add);
		$this->assertEquals(UserAccountPermissionValues::getValue($post['who_can_comment']), $collection->who_can_comment);
		$this->assertEquals($post['url'], $collection->url);
		$this->assertEquals($post['url_title'], $collection->url_title);
	}

	public function testUpdateHttpSeeEnumValidationErrors()
	{
		$collection = Collection::factory()->create();

		$post = [
			'title' => $this->faker->realText(100),
			'status' => 'test',
			'who_can_see' => 'test',
			'who_can_add' => 'test'
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', ['collection' => $collection]), $post)
			->assertStatus(302);
		//dump(session('errors'));
		$response->assertSessionHasErrors([
			'status' => __('validation.in', ['attribute' => __('collection.status')]),
			'who_can_add' => __('validation.enum_key', ['attribute' => __('collection.who_can_add')]),
			'who_can_comment' => __('validation.required', ['attribute' => __('collection.who_can_comment')]),
		]);
	}


	public function testUpdateStatusValidationError()
	{
		$collection = Collection::factory()->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'who_can_add' => UserAccountPermissionValues::getRandomKey(),
			'who_can_comment' => UserAccountPermissionValues::getRandomKey(),
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', ['collection' => $collection]), $post);
		var_dump(session('errors'));
		$response->assertRedirect()
			->assertSessionHasErrors(['status' => __('validation.required', ['attribute' => __('collection.status')])]);
	}

	public function testUpdateStatusInValidationError()
	{
		$collection = Collection::factory()->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => '345345',
			'who_can_add' => UserAccountPermissionValues::getRandomKey(),
			'who_can_comment' => UserAccountPermissionValues::getRandomKey(),
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', ['collection' => $collection]), $post);
		var_dump(session('errors'));
		$response->assertRedirect()
			->assertSessionHasErrors(['status' => __('validation.in', ['attribute' => __('collection.status')])]);
	}

	public function testWhoCanAddAndCommentMustEqualsMeIfStatusPrivate()
	{
		$collection = Collection::factory()->accepted()->create();

		$post = [
			'title' => Str::random(8) . ' ' . $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => StatusEnum::Private,
			'who_can_add' => 'everyone',
			'who_can_comment' => 'everyone',
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', ['collection' => $collection]), $post);
		var_dump(session('errors'));
		$response->assertRedirect()
			->assertSessionHasErrors([
				'who_can_add' => __('collection.validation.equals_value_if_other_field_equals',
					[
						'attribute' => __('collection.who_can_add'),
						'value' => __('collection.who_can_add_array.me'),
						'other_attribute' => __('collection.status'),
						'other_value' => __('collection.status_array.' . StatusEnum::Private)
					]),
				'who_can_comment' => __('collection.validation.equals_value_if_other_field_equals',
					[
						'attribute' => __('collection.who_can_comment'),
						'value' => __('collection.who_can_comment_array.me'),
						'other_attribute' => __('collection.status'),
						'other_value' => __('collection.status_array.' . StatusEnum::Private)
					])
			]);
	}

	public function testYouCantSelectWhoCanAddMeValueIfOtherUserBooksInCollection()
	{
		$collection = Collection::factory()->accepted()->create();

		$collectedBook = CollectedBook::factory()->create(['collection_id' => $collection->id]);

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => StatusEnum::Private,
			'who_can_add' => 'me',
			'who_can_comment' => 'me'
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', $collection), $post)
			->assertRedirect();
		var_dump(session('errors'));
		$response->assertSessionHasErrors(['who_can_add' =>
			__('collection.you_cant_select_only_me_because_there_are_books_added_by_other_users_in_the_collection',
				['value' => __('collection.who_can_add_array.me')])
		]);
	}

	public function testYouCanSelectWhoCanAddMeValueIfNoOtherUserBooksInCollection()
	{
		$collection = Collection::factory()->accepted()->create();

		$collectedBook = CollectedBook::factory()->create([
				'collection_id' => $collection->id,
				'create_user_id' => $collection->create_user_id
			]);

		$post = [
			'title' => $this->faker->realText(100) . ' ' . Str::random(3),
			'description' => $this->faker->realText(100) . ' ' . Str::random(3),
			'status' => StatusEnum::Private,
			'who_can_add' => 'me',
			'who_can_comment' => 'me'
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', $collection), $post)
			->assertRedirect()
			->assertSessionHasNoErrors();
	}

	public function testYouCanSelectWhoCanAddMeValueIfBookAttachedByCollectionUser()
	{
		$collection = Collection::factory()->accepted()->create();

		$collectedBook = CollectedBook::factory()->create([
				'collection_id' => $collection->id
			]);

		$collectionUser = CollectionUser::factory()->create([
				'collection_id' => $collection->id,
				'user_id' => $collectedBook->create_user_id
			]);

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => StatusEnum::Private,
			'who_can_add' => 'me',
			'who_can_comment' => 'me'
		];

		$response = $this->actingAs($collection->create_user)
			->patch(route('collections.update', $collection), $post)
			->assertRedirect()
			->assertSessionHasNoErrors();
	}
}
