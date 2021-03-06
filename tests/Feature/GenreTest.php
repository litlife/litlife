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
        $user = User::factory()->admin()->create();

        $str = Str::random(16);
        $fb2_code = $this->faker->word;

        $genre = Genre::factory()->main_genre()->create();

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
        $this->assertNotNull($genre->slug);
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

        $sequence = Genre::factory()->create(['name' => $str]);

        $response = $this->get(route('genres.search', ['q' => $sequence->id]))
            ->assertOk()
            ->assertJsonFragment([$sequence->name]);
    }

    public function testSearchByName()
    {
        $str = Str::random(10);

        $sequence = Genre::factory()->create(['name' => $str]);

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
