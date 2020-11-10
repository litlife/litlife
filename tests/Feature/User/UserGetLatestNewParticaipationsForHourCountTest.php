<?php

namespace Tests\Feature\User;

use App\Message;
use App\User;
use Carbon\Carbon;
use Tests\TestCase;

class UserGetLatestNewParticaipationsForHourCountTest extends TestCase
{
	public function testLatestParticipationsForHourCount()
	{
		$user = User::factory()->create();

		$user1 = User::factory()->create();

		$message1 = Message::factory()->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user1->id
			])->fresh();

		$this->assertEquals(1, $user->latest_new_particaipations_for_hour_count());

		$user2 = User::factory()->create();

		$message2 = Message::factory()->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user2->id
			])->fresh();

		$this->assertEquals(2, $user->latest_new_particaipations_for_hour_count());

		$user3 = User::factory()->create();

		$message3 = Message::factory()->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user3->id
			])->fresh();

		$this->assertEquals(3, $user->latest_new_particaipations_for_hour_count());

		$message4 = Message::factory()->create([
				'create_user_id' => $user3->id,
				'recepient_id' => $user->id
			])->fresh();

		$this->assertEquals(2, $user->latest_new_particaipations_for_hour_count());

		$message2->deleteForUser($user);

		$this->assertEquals(1, $user->latest_new_particaipations_for_hour_count());

		$time = now()->addHour()->addMinute();

		Carbon::setTestNow($time);

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());

		//

		$message = Message::factory()->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user1
			])->fresh();

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());

		$message = Message::factory()->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user2
			])->fresh();

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());

		$message = Message::factory()->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user3
			])->fresh();

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());
	}

}
