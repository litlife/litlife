<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\User;
use Tests\TestCase;

class PostUpdateTest extends TestCase
{
	public function testIfTopicDeleted()
	{
		$post = factory(Post::class)
			->create();

		$post->topic->delete();

		$this->get(route('posts.edit', $post))
			->assertStatus(401);

		$post->topic->forceDelete();

		$this->get(route('posts.edit', $post))
			->assertStatus(401);
	}

	public function testEditHttp()
	{
		$post = factory(Post::class)->create();

		$admin = factory(User::class)
			->states('administrator')
			->create();

		$this->actingAs($admin)
			->get(route('posts.edit', $post))
			->assertOk();
	}

	public function testEditHttpSeeValidationErrors()
	{
		$post = factory(Post::class)->create();

		$admin = factory(User::class)
			->states('administrator')
			->create();

		$this->actingAs($admin)
			->get(route('posts.edit', $post))
			->assertOk();

		$this->actingAs($admin)
			->followingRedirects()
			->patch(route('posts.update', $post))
			->assertSeeText(trans('validation.required', ['attribute' => __('post.bb_text')]));
	}

	public function testUpdateHttp()
	{
		$post = factory(Post::class)
			->create();

		$admin = factory(User::class)
			->states('administrator')
			->create();

		$text = $this->faker->text();

		$this->actingAs($admin)
			->patch(route('posts.update', $post), [
				'bb_text' => $text
			])
			->assertSessionHasNoErrors()
			->assertRedirect(route('posts.go_to', $post));

		$post->refresh();

		$this->assertEquals($text, $post->html_text);
		$this->assertEquals($post->getCharacterCountInText($text), $post->characters_count);
	}

	public function testUpdateHttpStringContainsAsc194()
	{
		$admin = factory(User::class)
			->states('admin')
			->create();

		$post = factory(Post::class)
			->create(['create_user_id' => $admin->id]);

		$text = '   ';

		$this->assertStringContainsString(chr(194), $text);

		$this->actingAs($admin)
			->patch(route('posts.update', ['post' => $post]), [
				'bb_text' => $text
			])
			->assertRedirect()
			->assertSessionHasErrors(['bb_text' => trans('validation.required', ['attribute' => __('post.bb_text')])]);
	}

}
