<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\ForumGroup;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class ForumCreateTest extends TestCase
{
	public function testCreate()
	{
		$user = User::factory()->admin()->create();

		$forumGroup = ForumGroup::factory()->create();

		$this->actingAs($user)
			->get(route('forums.create', ['forum_group_id' => $forumGroup->id]))
			->assertOk();
	}

	public function testStore()
	{
		$user = User::factory()->admin()->create();

		$forumGroup = ForumGroup::factory()->create();

		$forumNew = factory(Forum::class)->make();

		$this->actingAs($user)
			->post(route('forums.store', ['forum_group_id' => $forumGroup->id]), $forumNew->toArray())
			->assertSessionHasNoErrors()
			->assertRedirect();

		$forum = $forumGroup->forums()->first();

		$this->assertEquals($forumNew->name, $forum->name);
		$this->assertEquals($forumNew->description, $forum->description);
		$this->assertFalse($forum->private);
		$this->assertEquals($forum->min_message_count, $forum->min_message_count);
		$this->assertEquals(0, $forum->users_with_access()->count());
	}

	public function testStorePrivate()
	{
		$user = User::factory()->create();
		$user->group->add_forum_forum = true;
		$user->push();

		$other_user = User::factory()->create();

		$forum_group = ForumGroup::factory()->create();

		$response = $this->actingAs($user)
			->post(route('forums.store', ['forum_group_id' => $forum_group->id]),
				[
					'name' => $this->faker->realText(80) . ' ' . Str::random(10),
					'description' => $this->faker->realText(200),
					'private' => true,
					'private_users' => [$other_user->id],
					'min_message_count' => 0
				]
			);
		//dump(session());
		$response->assertSessionHasNoErrors()
			->assertRedirect(route('forums.index'));

		$user->refresh();

		$forum = $user->created_forums()->first();

		$this->assertNotNull($forum);

		$this->assertNotNull($forum->user_access->where('user_id', $user->id)->first());
		$this->assertNotNull($forum->user_access->where('user_id', $other_user->id)->first());

		$other_user2 = User::factory()->create();

		$this->assertNull($forum->user_access->where('user_id', $other_user2->id)->first());
	}
}
