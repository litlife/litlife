<?php

namespace Tests\Feature\Book;

use App\Book;
use App\Genre;
use Tests\TestCase;

class BookGenreHelperTest extends TestCase
{
	public function test()
	{
		$book = factory(Book::class)
			->create();

		$book->genres()->detach();

		$this->assertEquals(0, $book->genres()->count());

		$genres = Genre::inRandomOrder()->notMain()->limit(2)->get();

		$book->genres()->sync($genres->pluck('id')->toArray());
		$book->save();
		$book->refresh();

		$this->assertEquals(2, $book->genres()->count());
		$this->assertEquals('{' . implode(',', $genres->pluck('id')->toArray()) . '}',
			$book->genres_helper);

		$book->genres()->detach($genres[0]->id);
		$book->save();
		$book->refresh();

		$this->assertEquals(1, $book->genres()->count());
		$this->assertEquals('{' . $genres[1]->id . '}',
			$book->genres_helper);
	}

	public function testUpdatedAfterEdit()
	{
		$book = factory(Book::class)
			->states('with_writer', 'with_create_user', 'private')
			->create();

		$user = $book->create_user;

		$genre = factory(Genre::class)->create();
		$genre2 = factory(Genre::class)->create();
		$genre3 = factory(Genre::class)->create();
		$genre4 = factory(Genre::class)->create();

		$post = [
			'title' => 'текст ' . $book->title,
			'is_si' => true,
			'genres' => [$genre->id, $genre2->id, $genre3->id],
			'writers' => $book->writers()->any()->pluck('id')->toArray(),
			'ti_lb' => 'RU', 'ti_olb' => 'RU', 'ready_status' => 'complete'
		];

		$this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . ',' . $genre3->id . '}', $book->fresh()->genres_helper);

		//

		$post['genres'] = [$genre->id, $genre2->id];

		$this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . '}', $book->fresh()->genres_helper);

		//

		$post['genres'] = [$genre->id, $genre2->id, $genre4->id];

		$this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . ',' . $genre4->id . '}', $book->fresh()->genres_helper);

		//

		$post['genres'] = [$genre->id, $genre2->id];

		$this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . '}', $book->fresh()->genres_helper);

		$post['genres'] = [$genre2->id, $genre->id];

		$this->actingAs($user)
			->patch(route('books.update', $book), $post)
			->assertSessionHasNoErrors()
			->assertRedirect(route('books.edit', $book));

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . '}', $book->fresh()->genres_helper);
	}

	public function testSync()
	{
		$book = factory(Book::class)
			->create();

		$genre = factory(Genre::class)->create();
		$genre2 = factory(Genre::class)->create();
		$genre3 = factory(Genre::class)->create();

		$book->genres()->sync([$genre->id, $genre2->id, $genre3->id]);
		$book->save();
		$book->refresh();

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . ',' . $genre3->id . '}', $book->genres_helper);

		$book->genres()->sync([$genre2->id, $genre->id]);
		$book->save();
		$book->refresh();

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . '}', $book->genres_helper);

		$book->genres()->attach([$genre3->id]);
		$book->save();
		$book->refresh();

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . ',' . $genre3->id . '}', $book->genres_helper);

		$book->genres()->detach([$genre3->id]);
		$book->save();
		$book->refresh();

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . '}', $book->genres_helper);

		$book->genres()->detach();
		$book->save();
		$book->refresh();

		$this->assertEquals('{}', $book->genres_helper);

		$book->genres()->syncWithoutDetaching([$genre->id]);
		$book->genres()->syncWithoutDetaching([$genre2->id]);
		$book->save();
		$book->refresh();

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . '}', $book->genres_helper);
	}

	public function testUpdate()
	{
		$book = factory(Book::class)->create();

		$genre = factory(Genre::class)->create();
		$genre2 = factory(Genre::class)->create();

		$book->genres()->sync([$genre->id, $genre2->id]);

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . '}', $book->genres_helper);

		$book->genres_helper = null;
		$book->save();
		$book->refresh();

		$this->assertNull($book->genres_helper);

		$book->refreshGenresHelper();
		$book->save();
		$book->refresh();

		$this->assertEquals('{' . $genre->id . ',' . $genre2->id . '}', $book->genres_helper);
	}
}
