<?php

namespace Tests\Feature\User;

use App\User;
use App\UserSearchSetting;
use Tests\TestCase;

class UserSearchSettingsTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testCreate()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.books.search.settings.store', [
				'name' => 'read_access',
				'value' => 'open'
			]))
			->assertJson(['status' => 'saved']);

		$user_search_setting = $user->booksSearchSettings()->first();

		$this->assertNotNull($user_search_setting);
		$this->assertEquals('read_access', $user_search_setting->name);
		$this->assertEquals('open', $user_search_setting->value);
	}

	public function testUpdate()
	{
		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('users.books.search.settings.store', [
				'name' => 'read_access',
				'value' => 'open'
			]))
			->assertJson(['status' => 'saved']);

		$user_search_setting = $user->booksSearchSettings()->first();

		$this->assertNotNull($user_search_setting);
		$this->assertEquals('read_access', $user_search_setting->name);
		$this->assertEquals('open', $user_search_setting->value);

		$this->actingAs($user)
			->get(route('users.books.search.settings.store', [
				'name' => 'read_access',
				'value' => 'any'
			]))
			->assertJson(['status' => 'saved']);

		$user_search_setting = $user->booksSearchSettings()->first();

		$this->assertNotNull($user_search_setting);
		$this->assertEquals('read_access', $user_search_setting->name);
		$this->assertEquals('any', $user_search_setting->value);
	}

	public function testResourceDefaultValue()
	{
		$setting = factory(UserSearchSetting::class)
			->create([
				'name' => 'download_access',
				'value' => 'close'
			]);

		$user = $setting->user;

		$response = $this->actingAs($user)
			->get(route('books'))
			->assertOk();

		$resource = $response->original->gatherData()['resource'];

		$this->assertTrue($resource->isSaveSetting());
		$this->assertEquals('close', $resource->getDefaultInputValue('download_access'));
		$this->assertEquals('close', $resource->getInputValue('download_access'));
	}

	public function testResourceViewDefaultValue()
	{
		$setting = factory(UserSearchSetting::class)
			->create([
				'name' => 'view',
				'value' => 'table'
			]);

		$user = $setting->user;

		$response = $this->actingAs($user)
			->get(route('books'))
			->assertOk();

		$resource = $response->original->gatherData()['resource'];

		$this->assertTrue($resource->isSaveSetting());
		$this->assertEquals('table', $resource->getDefaultInputValue('view'));
		$this->assertEquals('table', $resource->getInputValue('view'));
	}
}
