<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use App\Topic;
use Tests\TestCase;

class PostGetTopicPageTest extends TestCase
{
	public function test()
	{
		$topic = Topic::factory()->create([
			'post_desc' => true
		]);

		$posts_on_page = 3;

		$posts_count = round($posts_on_page * 1.5);

		$posts = factory(Post::class, $posts_count)
			->create(['topic_id' => $topic->id]);

		$posts = $topic->postsOrderedBySetting()->get();

		foreach ($posts as $number => $post) {
			//dump($post->id.' '.$post->getTopicPage($posts_on_page));

			$number++;

			if ($number <= $posts_on_page)
				$this->assertEquals(1, $post->getTopicPage($posts_on_page));

			if ($number > $posts_on_page)
				$this->assertEquals(2, $post->getTopicPage($posts_on_page));
		}

		// post desc

		$topic->post_desc = false;
		$topic->push();

		$posts = $topic->postsOrderedBySetting()->get();

		foreach ($posts as $number => $post) {
			//dump($post->id.' '.$post->getTopicPage($posts_on_page));

			$number++;

			if ($number <= $posts_on_page)
				$this->assertEquals(1, $post->getTopicPage($posts_on_page));

			if ($number > $posts_on_page)
				$this->assertEquals(2, $post->getTopicPage($posts_on_page));
		}
	}

	public function testIfPostFixed()
	{
		$topic = Topic::factory()->create([
			'post_desc' => true
		]);

		$posts_on_page = 3;

		$posts_count = round($posts_on_page * 2);

		$posts = factory(Post::class, $posts_count)
			->create(['topic_id' => $topic->id]);

		// fix post

		$fixed_post = $topic->posts()->inRandomOrder()->first();
		$fixed_post->fix();

		$this->assertTrue($fixed_post->isFixed());

		// posts desc order

		$posts = $topic->postsOrderedBySetting()
			->where('id', '!=', $fixed_post->id)
			->get();

		foreach ($posts as $number => $post) {
			//dump($post->id.' '.$post->getTopicPage($posts_on_page));

			$number++;

			if ($number <= $posts_on_page)
				$this->assertEquals(1, $post->getTopicPage($posts_on_page));

			if ($number > $posts_on_page)
				$this->assertEquals(2, $post->getTopicPage($posts_on_page));
		}

		// posts asc order

		$topic->post_desc = false;
		$topic->push();

		$posts = $topic->postsOrderedBySetting()
			->where('id', '!=', $fixed_post->id)
			->get();

		foreach ($posts as $number => $post) {
			$number++;

			if ($number <= $posts_on_page)
				$this->assertEquals(1, $post->getTopicPage($posts_on_page));

			if ($number > $posts_on_page)
				$this->assertEquals(2, $post->getTopicPage($posts_on_page));
		}
	}
}
