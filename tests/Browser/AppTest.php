<?php

namespace Tests\Browser;

use App\Author;
use App\Book;
use App\Post;
use App\User;
use Illuminate\Support\Str;
use Tests\DuskTestCase;

class AppTest extends DuskTestCase
{
	public function test404Error()
	{
		$this->browse(function ($browser) {

			$browser->visit('/' . Str::random(16))
				->assertSee(__('app.name'))
				->assertSee(__('common.error') . ' 404')
				->assertSee(__('error.404'));
		});
	}

	public function test403Error()
	{
		$this->browse(function ($browser) {

			$user = User::factory()->create();

			$browser->loginAs($user)
				->visit('/groups/create')
				->assertSee(__('app.name'))
				->assertSee(__('common.error') . ' 403')
				->assertSee(__('error.403'));
		});
	}

	public function test401Error()
	{
		$this->browse(function ($browser) {

			$browser->visit('/groups/create')
				->assertSee(__('app.name'))
				->assertSee(__('error.401'));
		});
	}

	public function testWindowSharedData()
	{
		$this->browse(function ($browser) {

			$author = Author::factory()->create();

			$browser->visit(route('authors.show', $author))
				->assertSee(__('app.name'));

			$book = Book::factory()->create();

			$browser->visit(route('books.show', $book))
				->assertSee(__('app.name'));

			$post = Post::factory()->create();

			$topic = $post->topic;

			$browser->visit(route('topics.show', $topic))
				->assertSee(__('app.name'));

			$this->assertEquals(['topic_id' => $topic->id],
				$browser->driver->executeScript('return window[\'sharedData\'];'));

			$this->assertEquals(1,
				$browser->driver->executeScript("return $('head script:contains(sharedData)').length;"));
		});
	}

	public function testSEOTagNotRepeated()
	{
		$this->browse(function ($browser) {

			$browser->visit('/')->assertSee(__('app.name'));
			$browser->visit('/')->assertSee(__('app.name'));

			$this->assertEquals(1,
				$browser->driver->executeScript("return $('head meta[name=\"description\"]').length;"));

			$this->assertEquals(1,
				$browser->driver->executeScript("return $('head meta[name=\"keywords\"]').length;"));

			$this->assertEquals(1,
				$browser->driver->executeScript("return $('head meta[name=\"twitter:card\"]').length;"));

			$this->assertEquals(1,
				$browser->driver->executeScript("return $('head meta[property=\"og:url\"]').length;"));

			$this->assertEquals(1,
				$browser->driver->executeScript("return $('head meta[name=\"twitter:url\"]').length;"));
		});
	}

	public function test419Error()
	{
		$this->browse(function ($browser) {

			$password = uniqid();

			$user = factory(User::class)
				->states('with_confirmed_email')
				->create([
					'password' => $password
				]);

			$email = $user->emails()->first();

			$this->assertNotNull($email);

			$browser->visit(route('home'))
				->type('login', $email->email)
				->type('login_password', $password);

			$browser->driver->executeScript("$('[name=\"_token\"]').attr('value', '123');");

			$browser->press(__('auth.enter'))
				->assertSee(__('common.error'))
				->assertSee(__('error.419'))
				->assertSee('CSRF token mismatch');
		});
	}

}

