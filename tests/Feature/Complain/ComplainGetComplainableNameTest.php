<?php

namespace Tests\Feature\Complain;

use App\Complain;
use App\Post;
use Tests\TestCase;

class ComplainGetComplainableNameTest extends TestCase
{
	public function testGetComplainableName()
	{
		$complain = factory(Complain::class)
			->states('comment', 'sent_for_review')
			->create();

		$this->assertEquals('comment', $complain->getComplainableName());

		$post = factory(Post::class)->create();

		$complain->complainable_type = 'post';
		$complain->complainable_id = $post->id;
		$complain->save();
		$complain->refresh();

		$this->assertEquals('post', $complain->getComplainableName());

		$complain->complainable->forceDelete();
		$complain->refresh();

		$this->assertNull($complain->getComplainableName());
	}
}
