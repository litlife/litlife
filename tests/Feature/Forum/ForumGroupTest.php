<?php

namespace Tests\Feature\Forum;

use App\Enums\VariablesEnum;
use App\ForumGroup;
use App\User;
use App\Variable;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ForumGroupTest extends TestCase
{
	public function testPermissions()
	{
		$admin = factory(User::class)->create();
		$admin->group->forum_group_handle = true;
		$admin->push();

		$user = factory(User::class)->create();
		$user->group->forum_group_handle = false;
		$user->push();

		$this->assertTrue($admin->can('create', ForumGroup::class));
		$this->assertFalse($user->can('create', ForumGroup::class));

		$forum_group = factory(ForumGroup::class)->create();
		$forum_group->push();

		$this->assertTrue($admin->can('update', $forum_group));
		$this->assertFalse($user->can('update', $forum_group));

		$this->assertTrue($admin->can('delete', $forum_group));
		$this->assertFalse($user->can('delete', $forum_group));

		$this->assertTrue($admin->can('restore', $forum_group));
		$this->assertFalse($user->can('restore', $forum_group));

		$this->assertTrue($admin->can('change_order', ForumGroup::class));
		$this->assertFalse($user->can('change_order', ForumGroup::class));
	}

	public function testUpdateWithImageHttp()
	{
		$user = factory(User::class)
			->states('admin')
			->create();

		$title = $this->faker->realText(100);

		$jpeg_image_path = __DIR__ . '/../images/test.jpeg';
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

	public function testEmptyGetSort()
	{
		Variable::where('name', VariablesEnum::getValue('ForumGroupSort'))
			->delete();

		$forumGroup = factory(ForumGroup::class)
			->create();

		$this->assertNull($forumGroup->getSort());
	}
}
