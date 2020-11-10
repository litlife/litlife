<?php

namespace Tests\Feature\Sequence;

use App\Enums\StatusEnum;
use App\User;
use Tests\TestCase;

class SequenceCreateTest extends TestCase
{
	public function testCreate()
	{
		$user = User::factory()->create();

		$this->actingAs($user)
			->get(route('sequences.create'))
			->assertOk();
	}

	public function testStoreHttp()
	{
		$user = User::factory()->create();

		$name = $this->faker->realText(100);
		$description = $this->faker->realText(100);

		$response = $this->actingAs($user)
			->post(route('sequences.store'),
				[
					'name' => $name,
					'description' => $description
				]);

		//dump(session('errors'));

		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$sequence = $user->created_sequences()->first();

		$this->assertEquals($name, $sequence->name);
		$this->assertEquals($description, $sequence->description);
		$this->assertEquals(StatusEnum::Private, $sequence->status);
	}
}
