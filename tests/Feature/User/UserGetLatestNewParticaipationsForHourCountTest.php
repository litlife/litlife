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
		$user = factory(User::class)
			->create();

		$user1 = factory(User::class)->create();

		$message1 = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user1->id
			])->fresh();

		$this->assertEquals(1, $user->latest_new_particaipations_for_hour_count());

		$user2 = factory(User::class)->create();

		$message2 = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user2->id
			])->fresh();

		$this->assertEquals(2, $user->latest_new_particaipations_for_hour_count());

		$user3 = factory(User::class)->create();

		$message3 = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user3->id
			])->fresh();

		$this->assertEquals(3, $user->latest_new_particaipations_for_hour_count());

		$message4 = factory(Message::class)
			->create([
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

		$message = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user1
			])->fresh();

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());

		$message = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user2
			])->fresh();

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());

		$message = factory(Message::class)
			->create([
				'create_user_id' => $user->id,
				'recepient_id' => $user3
			])->fresh();

		$this->assertEquals(0, $user->latest_new_particaipations_for_hour_count());
	}

}
