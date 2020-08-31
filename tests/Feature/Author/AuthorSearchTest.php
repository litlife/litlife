<?php

namespace Tests\Feature\Author;

use App\Author;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthorSearchTest extends TestCase
{
	public function testSearch()
	{
		$author = factory(Author::class)
			->create(['last_name' => uniqid(), 'first_name' => uniqid(), 'nickname' => uniqid()]);

		$this->get(route('authors', ['search' => $author->last_name]))
			->assertOk()
			->assertSeeText($author->name);

		$this->get(route('authors', ['search' => $author->first_name]))
			->assertOk()
			->assertSeeText($author->name);

		$this->get(route('authors', ['search' => $author->nickname]))
			->assertOk()
			->assertSeeText($author->name);
	}

	public function testIntOverflow()
	{
		$this->get(route('authors.search', ['q' => '105775105775']))
			->assertOk();
	}

	public function testInt()
	{
		$author = factory(Author::class)
			->states('accepted')
			->create();

		$this->get(route('authors.search', ['q' => $author->id]))
			->assertOk()
			->assertJsonFragment($author->toArray());
	}

	public function testName()
	{
		$last_name = Str::random(16);

		$author = factory(Author::class)
			->states('accepted')
			->create(['last_name' => $last_name]);

		// https://litlife.club/posts/697632/go_to
		$this->assertNotNull($author->toArray()['fullName']);

		$this->get(route('authors.search', ['q' => mb_substr($last_name, 0, 14)]))
			->assertOk()
			->assertJsonFragment($author->toArray())
			// https://litlife.club/posts/697632/go_to
			->assertJsonFragment(['fullName' => $author->fullName]);
	}

	public function testFulltextSearchScopeSpecialSymbols()
	{
		$last_name = Str::random(10);

		$book = factory(Author::class)->create(['last_name' => 'ё' . $last_name]);

		$this->assertEquals(1, Author::query()->fulltextSearch('ё' . $last_name)->count());
		$this->assertEquals(1, Author::query()->fulltextSearch('е' . $last_name)->count());
	}

	public function testCreatedAtAscOrder()
	{
		$last_name = Str::random(16);

		$author = factory(Author::class)->states('accepted')
			->create([
				'last_name' => $last_name
			]);

		$author2 = factory(Author::class)->states('accepted')
			->create([
				'last_name' => $last_name,
				'created_at' => $author->created_at->addSeconds(10)
			]);

		$this->get(route('authors', ['search' => mb_substr($last_name, 0, 14), 'order' => 'created_at_desc']))
			->assertOk()
			->assertSeeTextInOrder([$author2->first_name, $author->first_name]);

		$this->get(route('authors', ['search' => mb_substr($last_name, 0, 14), 'order' => 'created_at_asc']))
			->assertOk()
			->assertSeeTextInOrder([$author->first_name, $author2->first_name]);
	}

	public function testPerPage()
	{
		$response = $this->get(route('authors', ['per_page' => 5]))
			->assertOk();

		$this->assertEquals(10, $response->original->gatherData()['authors']->perPage());

		$response = $this->get(route('authors', ['per_page' => 200]))
			->assertOk();

		$this->assertEquals(100, $response->original->gatherData()['authors']->perPage());
	}

	public function testUserReaded()
	{
		$user = factory(User::class)->create();

		$response = $this->actingAs($user)
			->get(route('users.authors.readed', $user))
			->assertOk();
	}

	public function testUserReadLater()
	{
		$user = factory(User::class)->create();

		$response = $this->actingAs($user)
			->get(route('users.authors.read_later', $user))
			->assertOk();
	}

	public function testUserReadNotComplete()
	{
		$user = factory(User::class)->create();

		$response = $this->actingAs($user)
			->get(route('users.authors.read_not_complete', $user))
			->assertOk();
	}

	public function testUserReadNow()
	{
		$user = factory(User::class)->create();

		$response = $this->actingAs($user)
			->get(route('users.authors.read_now', $user))
			->assertOk();
	}

	public function testUserNotRead()
	{
		$user = factory(User::class)->create();

		$response = $this->actingAs($user)
			->get(route('users.authors.not_read', $user))
			->assertOk();
	}

	public function testUserCreated()
	{
		$user = factory(User::class)->create();

		$response = $this->actingAs($user)
			->get(route('users.authors.created', $user))
			->assertOk();
	}

	public function testOrderByRating()
	{
		$response = $this
			->get(route('authors', ['order' => 'rating_day_desc']))
			->assertOk();

		$response = $this
			->get(route('authors', ['order' => 'rating_week_desc']))
			->assertOk();

		$response = $this
			->get(route('authors', ['order' => 'rating_quartal_desc']))
			->assertOk();

		$response = $this
			->get(route('authors', ['order' => 'rating_month_desc']))
			->assertOk();

		$response = $this
			->get(route('authors', ['order' => 'rating_year_desc']))
			->assertOk();
	}

	public function testShowUserAuthorsFirst()
	{
		$str = Str::random(16);

		$author = factory(Author::class)
			->states('accepted', 'with_author_manager')
			->create(['nickname' => $str]);

		$manager = $author->managers()->first();
		$user = $manager->user;

		$response = $this->actingAs($user)
			->get(route('authors.search', ['q' => mb_substr($str, 0, 2)]))
			->assertOk();

		$data = $response->decodeResponseJson()['data'];

		$this->assertNotEmpty($data);
		$this->assertEquals($author->id, pos($data)['id']);

		$response
			->assertJsonFragment(['fullName' => $author->fullName])
			->assertJsonFragment(['id' => $author->id]);
	}

	public function testFullTextSearch()
	{
		$author = factory(Author::class)
			->states('accepted')
			->create(['nickname' => 'EyyKOMRBa6clJDaI']);

		$this->assertGreaterThanOrEqual(1, Author::fulltextSearch('Eyyk')->get()->count());
		$this->assertGreaterThanOrEqual(0, Author::fulltextSearch('EYY')->get()->count());
		$this->assertGreaterThanOrEqual(1, Author::fulltextSearch('Ey')->get()->count());
	}
}
