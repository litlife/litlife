<?php

namespace Tests\Feature;

use App\Genre;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class GenreTest extends TestCase
{
	/**
	 * A basic feature test example.
	 *
	 * @return void
	 */
	public function testIndexHttp()
	{
		$this->get(route('genres'))
			->assertOk();
	}

	public function testGenresShowNotFound()
	{
		$this->get('/genres/opds')
			->assertNotFound();
	}

	public function testStore()
	{
		$user = factory(User::class)
			->states('admin')->create();

		$str = 'Название жанра';
		$fb2_code = 'test';

		$genre = factory(Genre::class)
			->states('main_genre')
			->create();

		$this->actingAs($user)
			->post(route('genres.store'), [
				'genre_group_id' => $genre->id,
				'name' => $str,
				'fb_code' => $fb2_code,
				'age' => '0'
			])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$genre = Genre::orderBy('id', 'desc')->first();

		$this->assertEquals($str, $genre->name);
		$this->assertEquals($fb2_code, $genre->fb_code);
		$this->assertEquals('nazvanie-zanra', $genre->slug);
		$this->assertEquals(0, $genre->age);
	}

	public function testSearchNumberWithDotIsOk()
	{
		$response = $this->get(route('genres.search', ['q' => '20.']))
			->assertOk();
	}

	public function testSearchByID()
	{
		$str = Str::random(10);

		$sequence = factory(Genre::class)->create(['name' => $str]);

		$response = $this->get(route('genres.search', ['q' => $sequence->id]))
			->assertOk()
			->assertJsonFragment([$sequence->name]);
	}

	public function testSearchByName()
	{
		$str = Str::random(10);

		$sequence = factory(Genre::class)->create(['name' => $str]);

		$response = $this->get(route('genres.search', ['q' => mb_substr($sequence->name, 0, 5)]))
			->assertOk()
			->assertJsonFragment([$sequence->name]);
	}

	public function testShowWhenNumberLong()
	{
		$response = $this->get(route('genres.show', ['genre' => '12335345456456456456456456456']))
			->assertNotFound();
	}
}
