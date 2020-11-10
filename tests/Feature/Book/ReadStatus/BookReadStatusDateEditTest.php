<?php

namespace Tests\Feature\Book\ReadStatus;

use App\BookStatus;
use App\Http\Middleware\RemeberSessionGeoIpAndBrowser;
use Tests\TestCase;

class BookReadStatusDateEditTest extends TestCase
{
	public function setUp(): void
	{
		parent::setUp(); // TODO: Change the autogenerated stub

		$this->withMiddleware(RemeberSessionGeoIpAndBrowser::class);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testEditHttpIsOk()
	{
		$user_read_status = BookStatus::factory()->create();

		$user = $user_read_status->user;
		$book = $user_read_status->book;

		$this->actingAs($user)
			->get(route('books.read_status.date.edit', ['book' => $book]))
			->assertOk()
			->assertViewIs('book.read_status.date.edit')
			->assertViewHas('book', $book)
			->assertViewHas('user_read_status', $user_read_status)
			->assertViewHas('user_updated_at', $user_read_status->user_updated_at->timezone(session()->get('geoip')->timezone));
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testEditHttpIfUserUpdatedAtIsNull()
	{
		$user_read_status = BookStatus::factory()->create(['user_updated_at' => null]);

		$user = $user_read_status->user;
		$book = $user_read_status->book;

		$this->assertNull($user_read_status->user_updated_at);

		$this->actingAs($user)
			->get(route('books.read_status.date.edit', ['book' => $book]))
			->assertOk()
			->assertViewIs('book.read_status.date.edit')
			->assertViewHas('book', $book)
			->assertViewHas('user_read_status', $user_read_status);
	}
}
