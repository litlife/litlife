<?php

namespace Tests\Feature\User;

use App\AchievementUser;
use Tests\TestCase;

class UserAchievementTest extends TestCase
{
	public function testShow()
	{
		$achievementUser = factory(AchievementUser::class)->create();

		$user = $achievementUser->user;

		$this->actingAs($user)
			->get(route('users.achievements', ['user' => $user]))
			->assertOk();
	}
}
