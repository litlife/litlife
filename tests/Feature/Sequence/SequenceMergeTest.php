<?php

namespace Tests\Feature\Sequence;

use App\Book;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Sequence;
use App\User;
use Tests\TestCase;

class SequenceMergeTest extends TestCase
{
	public function test()
	{
		config(['activitylog.enabled' => true]);

		$user = factory(User::class)->create();
		$user->group->sequence_merge = true;
		$user->save();

		$sequence = factory(Sequence::class)->create();
		$sequence->statusAccepted();
		$sequence->save();

		$sequence2 = factory(Sequence::class)->create();
		$sequence2->statusAccepted();
		$sequence2->save();

		$book = factory(Book::class)->create();
		$book2 = factory(Book::class)->create();

		$sequence2->books()->sync([$book->id]);
		$sequence->books()->sync([$book->id, $book2->id]);

		UpdateSequenceBooksCount::dispatch($sequence2);
		UpdateSequenceBooksCount::dispatch($sequence);

		$this->assertEquals(1, $sequence2->books()->count());
		$this->assertEquals(2, $sequence->books()->count());

		$this->actingAs($user)
			->get(route('sequences.merge_form', ['sequence' => $sequence2->id]))
			->assertOk();

		$response = $this->actingAs($user)
			->post(route('sequences.merge', ['sequence' => $sequence2->id]), [
				'merged_to_sequence_id' => $sequence->id
			]);
		//dump(session('errors'));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$sequence->refresh();
		$sequence2->refresh();

		$this->assertEquals($user->id, $sequence2->merge_user_id);
		$this->assertNotNull($sequence2->merged_at);
		$this->assertEquals($sequence->id, $sequence2->merged_to);
		$this->assertEquals(2, $sequence->books()->count());
		$this->assertEquals(0, $sequence2->books()->count());

		$this->assertEquals(1, $sequence2->activities()->count());
		$activity = $sequence2->activities()->first();
		$this->assertEquals('merged', $activity->description);
		$this->assertEquals($user->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);


		$response = $this->actingAs($user)
			->get(route('sequences.unmerge', ['sequence' => $sequence2->id]));
		$response->assertSessionHasNoErrors()
			->assertRedirect();

		$sequence->refresh();
		$sequence2->refresh();

		$this->assertNull($sequence2->merge_user_id);
		$this->assertNull($sequence2->merged_at);
		$this->assertNull($sequence2->merged_to);
		$this->assertEquals(2, $sequence->books()->count());
		$this->assertEquals(0, $sequence2->books()->count());
	}
}
