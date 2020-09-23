<?php

namespace Tests\Feature\Forum\ForumGroup;

use App\ForumGroup;
use App\User;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ForumGroupUpdateTest extends TestCase
{
	public function testUpdateWithImageHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$title = $this->faker->realText(100);

		$jpeg_image_path = __DIR__ . '/../../images/test.jpeg';
		$filename = 'test.jpeg';
		$image = new UploadedFile($jpeg_image_path, $filename, null, null, true);

		$forumGroup = factory(ForumGroup::class)
			->create();

		$this->assertNull($forumGroup->image);

		$this->actingAs($user)
			->patch(route('forum_groups.update', $forumGroup),
				[
					'name' => $title,
					'image' => $image
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$forumGroup->refresh();

		$this->assertEquals($title, $forumGroup->name);
		$this->assertNotNull($forumGroup->image);
		$this->assertNotNull($forumGroup->image->size);
	}

	public function testUpdateWithoutImageHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$title = $this->faker->realText(100);

		$forumGroup = factory(ForumGroup::class)
			->create();

		$this->assertNull($forumGroup->image);

		$this->actingAs($user)
			->patch(route('forum_groups.update', $forumGroup),
				[
					'name' => $title
				])
			->assertSessionHasNoErrors()
			->assertRedirect();

		$forumGroup->refresh();

		$this->assertEquals($title, $forumGroup->name);
		$this->assertNull($forumGroup->image);
	}
}
