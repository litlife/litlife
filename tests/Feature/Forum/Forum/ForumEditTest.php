<?php

namespace Tests\Feature\Forum\Forum;

use App\Forum;
use App\User;
use Tests\TestCase;

class ForumEditTest extends TestCase
{
    public function testUpdate()
    {
        $admin = User::factory()->admin()->create();

        $forum = Forum::factory()->create();

        $forumNew = Forum::factory()
            ->make();

        $response = $this->actingAs($admin)
            ->patch(route('forums.update', ['forum' => $forum->id]),
                $forumNew->toArray())
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $forum->refresh();

        $this->assertEquals($forumNew->title, $forum->title);
        $this->assertEquals($forumNew->description, $forum->description);
        $this->assertEquals($forumNew->private, $forum->private);
        $this->assertEquals($forumNew->min_message_count, $forum->min_message_count);
    }

    public function testUpdateAutofixFirstPostInCreatedTopicsHttp()
    {
        $admin = User::factory()->create();
        $admin->group->forum_edit_forum = true;
        $admin->push();

        $forum = Forum::factory()->create();

        $this->assertFalse($forum->isAutofixFirstPostInCreatedTopics());

        $response = $this->actingAs($admin)
            ->patch(route('forums.update', ['forum' => $forum->id]),
                array_merge($forum->toArray(), ['autofix_first_post_in_created_topics' => '1']))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $forum->refresh();

        $this->assertTrue($forum->isAutofixFirstPostInCreatedTopics());
    }

    public function testUpdateOrderTopicsBasedOnFixPostLikesHttp()
    {
        $admin = User::factory()->create();
        $admin->group->forum_edit_forum = true;
        $admin->push();

        $forum = Forum::factory()->create();

        $this->assertFalse($forum->isOrderTopicsBasedOnFixPostLikes());

        $response = $this->actingAs($admin)
            ->patch(route('forums.update', ['forum' => $forum->id]),
                array_merge($forum->toArray(), ['order_topics_based_on_fix_post_likes' => '1']))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $forum->refresh();

        $this->assertTrue($forum->isOrderTopicsBasedOnFixPostLikes());
    }

    public function testIdeaForumEnable()
    {
        $admin = User::factory()->create();
        $admin->group->forum_edit_forum = true;
        $admin->push();

        $forum = Forum::factory()->create();

        $this->assertFalse($forum->isIdeaForum());

        $response = $this->actingAs($admin)
            ->patch(route('forums.update', ['forum' => $forum->id]),
                array_merge($forum->toArray(), ['is_idea_forum' => '1']))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $forum->refresh();

        $this->assertTrue($forum->isIdeaForum());
    }
}
