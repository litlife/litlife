<?php

namespace Tests\Feature\Component;

use App\Author;
use App\View\Components\AuthorPhoto;
use Tests\TestCase;

class AuthorPhotoComponentTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testNotFound()
	{
		$author = null;

		$component = new AuthorPhoto($author, 200, 200);

		$expected = <<<'blade'
<img class="{{ $class }}" itemprop="image"
alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}"
data-src="{{ $url }}"/>
blade;

		$this->assertFalse($component->isShowPhoto());

		$this->assertEquals($expected, $component->render());

		$data = $component->data();

		$this->assertEquals(0, $data['href']);
		$this->assertEquals(null, $data['alt']);
		$this->assertStringContainsString('no_image_unknown.png', $data['url']);
		$this->assertEquals(200, $data['width']);
		$this->assertEquals(200, $data['height']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testAuthorPhotoDeleted()
	{
		$author = Author::factory()->with_photo()->create();

		$author->photo->delete();

		$component = new AuthorPhoto($author, 200, 200);

		$expected = <<<'blade'
<a title="{{ $alt }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto"><img class="{{ $class }}" itemprop="image"
alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}"
data-src="{{ $url }}"/></a>
blade;

		$this->assertFalse($component->isShowPhoto());

		$this->assertEquals($expected, $component->render());

		$data = $component->data();

		$this->assertEquals(route('authors.show', ['author' => $author]), $data['href']);
		$this->assertEquals($author->name, $data['alt']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testAuthorDeleted()
	{
		$author = Author::factory()->create();

		$author->delete();

		$component = new AuthorPhoto($author, 200, 200);

		$expected = <<<'blade'
<a title="{{ $alt }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto"><img class="{{ $class }}" alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}" data-src="{{ $url }}"/></a>
blade;

		$this->assertFalse($component->isShowPhoto());

		$data = $component->data();

		$this->assertEquals(null, $data['alt']);
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testAuthorPhotoExists()
	{
		$author = Author::factory()->with_photo()->create();

		$component = new AuthorPhoto($author, 200, 200);

		$expected = <<<'blade'
<a title="{{ $alt }}" href="{{ $href }}" class="text-decoration-none d-block mx-auto"><img class="{{ $class }}" itemprop="image"
alt="{{ $alt }}"
data-srcset="{{ $url2x }} 2x, {{ $url3x }} 3x"
style="{{ $style }}"
data-src="{{ $url }}"/></a>
blade;

		$this->assertTrue($component->isShowPhoto());

		$this->assertEquals($expected, $component->render());

		$data = $component->data();

		$this->assertEquals(route('authors.show', ['author' => $author]), $data['href']);
		$this->assertEquals($author->name, $data['alt']);
		$this->assertStringContainsString($author->photo->url, $data['url']);
		$this->assertEquals(200, $data['width']);
		$this->assertEquals(200, $data['height']);
	}

	public function testDontShowIfDontHaveAccess()
	{
		$author = Author::factory()->with_photo()->private()->create();

		$component = new AuthorPhoto($author, 200, 200);

		$this->assertFalse($component->isShowPhoto());

		$data = $component->data();

		$this->assertEquals(null, $data['alt']);
	}

	public function testShowIfHaveAccess()
	{
		$author = Author::factory()->with_photo()->private()->create();

		$this->be($author->create_user);

		$component = new AuthorPhoto($author, 200, 200);

		$this->assertTrue($component->isShowPhoto());

		$data = $component->data();

		$this->assertEquals($author->name, $data['alt']);
	}
}
