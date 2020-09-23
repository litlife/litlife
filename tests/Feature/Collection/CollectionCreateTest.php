<?php

namespace Tests\Feature\Collection;

use App\Enums\UserAccountPermissionValues;
use App\User;
use Tests\TestCase;

class CollectionCreateTest extends TestCase
{
	public function testCreateHttp()
	{
		$user = factory(User::class)->states('with_user_permissions')->create();

		$this->actingAs($user)
			->get(route('collections.create'))
			->assertOk();
	}

	public function testStoreHttp()
	{
		$user = factory(User::class)->states('with_user_permissions')->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => 3,
			'who_can_add' => 'me',
			'who_can_comment' => 'me',
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$response = $this->actingAs($user)
			->post(route('collections.store', $post))
			->assertSessionHasNoErrors();
		//dump(session('errors'));
		$response->assertRedirect(route('users.collections.created', $user));

		$collection = $user->created_collections()->first();

		$this->assertEquals($post['title'], $collection->title);
		$this->assertEquals($post['description'], $collection->description);
		$this->assertTrue($collection->isPrivate());
		$this->assertEquals(UserAccountPermissionValues::getValue($post['who_can_add']), $collection->who_can_add);
		$this->assertEquals(UserAccountPermissionValues::getValue($post['who_can_comment']), $collection->who_can_comment);
		$this->assertEquals($post['url'], $collection->url);
		$this->assertEquals($post['url_title'], $collection->url_title);
		$this->assertEquals(1, $collection->users_count);

		$user->refresh();

		$this->assertEquals(1, $user->data->created_collections_count);
	}

	public function testStoreNotPrivateHttp()
	{
		$user = factory(User::class)->states('with_user_permissions')->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'status' => 0,
			'who_can_add' => UserAccountPermissionValues::getRandomKey(),
			'who_can_comment' => UserAccountPermissionValues::getRandomKey(),
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$response = $this->actingAs($user)
			->post(route('collections.store', $post))
			->assertSessionHasNoErrors();
		//dump(session('errors'));
		$response->assertRedirect(route('users.collections.created', $user));

		$collection = $user->created_collections()->first();

		$this->assertTrue($collection->isAccepted());
	}

	public function testStoreStatusValidationError()
	{
		$user = factory(User::class)->create();

		$post = [
			'title' => $this->faker->realText(100),
			'description' => $this->faker->realText(100),
			'who_can_add' => UserAccountPermissionValues::getRandomKey(),
			'who_can_comment' => UserAccountPermissionValues::getRandomKey(),
			'url' => $this->faker->url,
			'url_title' => $this->faker->realText(50),
		];

		$response = $this->actingAs($user)
			->post(route('collections.store', $post))
			->assertRedirect();
		var_dump(session('errors'));
		$response->assertSessionHasErrors(['status' => __('validation.required', ['attribute' => __('collection.status')])]);
	}
}
