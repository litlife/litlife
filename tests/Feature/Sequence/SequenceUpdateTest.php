<?php

namespace Tests\Feature\Sequence;

use App\Sequence;
use App\User;
use Tests\TestCase;

class SequenceUpdateTest extends TestCase
{
	public function testEdit()
	{
		$user = factory(User::class)->create();
		$user->group->sequence_edit = true;
		$user->save();

		$sequence = factory(Sequence::class)->create();
		$sequence->statusAccepted();
		$sequence->save();

		$this->actingAs($user)
			->get(route('sequences.edit', ['sequence' => $sequence->id]))
			->assertOk();
	}

	public function testUpdateHttp()
	{
		$user = factory(User::class)->create();
		$user->group->sequence_edit = true;
		$user->save();

		$sequence = factory(Sequence::class)->create();
		$sequence->statusAccepted();
		$sequence->save();

		$name = $this->faker->realText(100) . ' "' . $this->faker->realText(50) . '"';
		$description = $this->faker->realText(100);

		$response = $this->actingAs($user)
			->patch(route('sequences.update', ['sequence' => $sequence->id]),
				[
					'name' => $name,
					'description' => $description
				]);

		//dump(session('errors'));

		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$sequence->refresh();

		$this->assertEquals($name, $sequence->name);
		$this->assertEquals($description, $sequence->description);
		$this->assertNotNull($sequence->user_edited_at);
	}
}
