<?php

namespace Tests\Feature;

use App\Achievement;
use App\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AchievementTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testCreateHttp()
	{
		$user = User::factory()->create();
		$user->group->achievement = true;
		$user->push();

		Storage::fake(config('filesystems.default'));

		$title = $this->faker->realText(20);
		$description = $this->faker->realText(50);

		$file = UploadedFile::fake()
			->image(__DIR__ . '/images/test.jpeg');

		$response = $this->actingAs($user)
			->json('POST',
				route('achievements.store'),
				[
					'title' => $title,
					'description' => $description,
					'image' => $file
				]
			)
			->assertSessionHasNoErrors()
			->assertRedirect(route('achievements.index'));

		$achievement = Achievement::whereTitle($title)
			->whereDescription($description)
			->latest()
			->first();

		$this->assertNotNull($achievement);
		$this->assertNotNull($achievement->image);
	}

	public function testAttachToUserHttp()
	{
		$admin = User::factory()->create();
		$admin->group->achievement = true;
		$admin->push();

		$user = User::factory()->create();
		$user->push();

		$achievement = Achievement::factory()->create();

		$response = $this->actingAs($admin)
			->json('POST',
				route('users.achievements.attach', compact('user')),
				[
					'achievement' => $achievement->id
				]
			)
			->assertSessionHasNoErrors()
			->assertRedirect();

		$user_achievement = $user->user_achievements()->first();

		$this->assertNotNull($user_achievement);
		$this->assertEquals($achievement->id, $user_achievement->achievement->id);
	}

	public function testAttachToUserPolicy()
	{
		$admin = User::factory()->create();

		$user = User::factory()->create();

		$this->assertFalse($admin->can('attach_achievement', $user));

		$admin->group->achievement = true;
		$admin->push();

		$this->assertTrue($admin->can('attach_achievement', $user));
	}

	public function testDetachFromUserPolicy()
	{
		$admin = User::factory()->with_user_group()->create();
		$admin->group->achievement = true;
		$admin->push();

		$this->assertTrue($admin->can('detach', Achievement::class));

		$user = User::factory()->create();

		$this->assertFalse($user->can('detach', Achievement::class));
	}

	public function testCreatePolicy()
	{
		$admin = User::factory()->with_user_group()->create();
		$admin->group->achievement = true;
		$admin->push();

		$this->assertTrue($admin->can('create', Achievement::class));

		$user = User::factory()->create();

		$this->assertFalse($user->can('create', Achievement::class));
	}

	public function testUpdatePolicy()
	{
		$admin = User::factory()->with_user_group()->create();
		$admin->group->achievement = true;
		$admin->push();

		$this->assertTrue($admin->can('update', Achievement::class));

		$user = User::factory()->create();

		$this->assertFalse($user->can('update', Achievement::class));
	}

	public function testDeletePolicy()
	{
		$achievement = Achievement::factory()->create();

		$admin = User::factory()->with_user_group()->create();
		$admin->group->achievement = true;
		$admin->push();

		$this->assertTrue($admin->can('delete', $achievement));

		$user = User::factory()->create();

		$this->assertFalse($user->can('delete', $achievement));
	}

	public function testRestorePolicy()
	{
		$achievement = Achievement::factory()->create();
		$achievement->delete();

		$admin = User::factory()->with_user_group()->create();
		$admin->group->achievement = true;
		$admin->push();

		$this->assertTrue($admin->can('restore', $achievement));

		$user = User::factory()->create();

		$this->assertFalse($user->can('restore', $achievement));
	}
}
