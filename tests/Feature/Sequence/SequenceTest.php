<?php

namespace Tests\Feature\Sequence;

use App\Book;
use App\Comment;
use App\Enums\StatusEnum;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Sequence;
use App\User;
use App\UserSequence;
use Illuminate\Support\Str;
use Tests\TestCase;

class SequenceTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testIndexHttp()
	{
		$this->get(route('sequences'))
			->assertOk();
	}

	public function testSearchAjax()
	{
		$this->get(route('sequences'), ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
			->assertOk();
	}

	public function testFullTextSearch()
	{
		$sequence = factory(Sequence::class)
			->create();
		$sequence->name = 'Время&—&детство!';
		$sequence->save();

		$sequence = Sequence::FulltextSearch($sequence->name)->get();

		$this->assertTrue(true);
	}

	public function testMergeSequences()
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

	public function testCreateHttp()
	{
		$user = factory(User::class)->create();
		$user->save();

		$this->get(route('sequences.create'))
			->assertStatus(401);

		$this->actingAs($user)
			->get(route('sequences.create'))
			->assertOk();

		$this->post(route('sequences.store'))
			->assertStatus(302);

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

	public function testUpdateHttp()
	{
		$user = factory(User::class)->create();
		$user->group->sequence_edit = true;
		$user->save();

		$sequence = factory(Sequence::class)->create();
		$sequence->statusAccepted();
		$sequence->save();

		$this->get(route('sequences.edit', ['sequence' => $sequence->id]))
			->assertStatus(401);

		$this->actingAs($user)
			->get(route('sequences.edit', ['sequence' => $sequence->id]))
			->assertOk();

		$this->patch(route('sequences.update', ['sequence' => $sequence->id]))
			->assertStatus(302);

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

	public function testBooksCountIfPrivate()
	{
		$user = factory(User::class)
			->create();

		$sequence = factory(Sequence::class)->create(['create_user_id' => $user->id]);
		$sequence->statusPrivate();
		$sequence->save();

		$book = factory(Book::class)->create(['create_user_id' => $user->id]);
		$book->statusPrivate();
		$book->save();

		$book2 = factory(Book::class)->create(['create_user_id' => $user->id]);
		$book2->statusPrivate();
		$book2->save();

		$sequence->books()->sync([$book->id, $book2->id]);

		$book->refresh();

		UpdateSequenceBooksCount::dispatch($sequence);

		$this->assertEquals(2, $sequence->book_count);
	}

	public function testDeleteHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->states('admin')->create();

		$sequence = factory(Sequence::class)->create();

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('sequences.delete', $sequence))
			->assertSeeText(__('sequence.deleted'));

		$sequence->refresh();

		$this->assertSoftDeleted($sequence);

		$this->assertEquals(1, $sequence->activities()->count());
		$activity = $sequence->activities()->first();
		$this->assertEquals('deleted', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testRestoreHttp()
	{
		config(['activitylog.enabled' => true]);

		$admin = factory(User::class)->states('admin')->create();

		$sequence = factory(Sequence::class)->create();
		$sequence->delete();

		$this->actingAs($admin)
			->followingRedirects()
			->get(route('sequences.delete', $sequence))
			->assertDontSeeText(__('sequence.deleted'));

		$sequence->refresh();

		$this->assertFalse($sequence->trashed());

		$this->assertEquals(1, $sequence->activities()->count());
		$activity = $sequence->activities()->first();
		$this->assertEquals('restored', $activity->description);
		$this->assertEquals($admin->id, $activity->causer_id);
		$this->assertEquals('user', $activity->causer_type);
	}

	public function testShowHttpOk()
	{
		$sequence = factory(Sequence::class)
			->states('with_book')
			->create();

		$this->assertEquals(1, $sequence->book_count);

		$book = $sequence->books()->first();

		$this->get(route('sequences.show', $sequence))
			->assertOk()
			->assertSeeText(trans_choice('book.books', 2) . ' 1')
			->assertSeeText($book->title);
	}

	public function testComments()
	{
		$sequence = factory(Sequence::class)
			->states('with_book')
			->create();

		$book = $sequence->books()->first();

		$comment = factory(Comment::class)
			->create(['commentable_type' => 'book', 'commentable_id' => $book->id]);

		$this->get(route('sequences.comments', ['sequence' => $sequence->id]))
			->assertOk()
			->assertSeeText($comment->text)
			->assertSee('comments-search-container');
	}

	public function testPerPage()
	{
		$response = $this->get(route('sequences', ['per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['sequences']->perPage());

		$response = $this->get(route('sequences', ['per_page' => 200]))
			->assertOk();

		$this->assertEquals(100, $response->original->gatherData()['sequences']->perPage());
	}

	public function testSearchNumberWithDotIsOk()
	{
		$response = $this->get(route('sequences.search', ['q' => '20.']))
			->assertOk();
	}

	public function testSearchByID()
	{
		$str = Str::random(10);

		$sequence = factory(Sequence::class)->create(['name' => $str]);

		$response = $this->get(route('sequences.search', ['q' => $sequence->id]))
			->assertOk()
			->assertJsonFragment([$sequence->name]);
	}

	public function testSearchByName()
	{
		$str = Str::random(10);

		$sequence = factory(Sequence::class)->create(['name' => $str]);

		$response = $this->get(route('sequences.search', ['q' => mb_substr($sequence->name, 0, 5)]))
			->assertOk()
			->assertJsonFragment([$sequence->name]);
	}

	public function testToggle()
	{
		$sequence = factory(Sequence::class)
			->create();

		$user = factory(User::class)
			->create();

		$this->actingAs($user)
			->get(route('sequences.favorites.toggle', ['sequence' => $sequence]))
			->assertOk()
			->assertJson([
				'result' => 'attached',
				'added_to_favorites_count' => 1
			]);

		$user->refresh();
		$sequence->refresh();

		$this->assertTrue($user->is($sequence->addedToFavoritesUsers()->first()));
		$this->assertEquals(1, $sequence->added_to_favorites_count);
		$this->assertTrue($sequence->is($user->sequences()->first()));

		$this->actingAs($user)
			->get(route('sequences.favorites.toggle', ['sequence' => $sequence]))
			->assertOk()
			->assertJson([
				'result' => 'detached',
				'added_to_favorites_count' => 0
			]);

		$sequence->refresh();

		$this->assertEquals(0, $sequence->added_to_favorites_count);
	}

	public function testToggleIfAuthorDeleted()
	{
		$user_sequence = factory(UserSequence::class)
			->create();

		$sequence = $user_sequence->sequence;
		$user = $user_sequence->user;

		$sequence->delete();

		$this->actingAs($user)
			->get(route('sequences.favorites.toggle', ['sequence' => $sequence]))
			->assertOk()
			->assertJson([
				'result' => 'detached',
				'added_to_favorites_count' => 0
			]);

		$sequence->refresh();

		$this->assertEquals(0, $sequence->added_to_favorites_count);
	}
}
