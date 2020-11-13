<?php

namespace Tests\Feature\Author;

use App\Author;
use App\AuthorGroup;
use App\User;
use Tests\TestCase;

class AuthorGroupTest extends TestCase
{
    public function testAuthorsGroupIndexHttp()
    {
        $author_group = AuthorGroup::factory()->with_two_authors()->create();

        $authors = $author_group->authors()->get();

        $author = $authors->first();
        $another_author = $authors->last();

        $admin = User::factory()->create();

        $this->actingAs($admin)
            ->get(route('authors.group.index', ['author' => $author]))
            ->assertOk()
            ->assertSeeText($author->name)
            ->assertSeeText($another_author->name);

        $this->get(route('authors.show', ['author' => $author]))
            ->assertOk()
            ->assertSeeText(__('author.other_pages'))
            ->assertSeeText($another_author->name);

        $this->get(route('authors.show', ['author' => $another_author]))
            ->assertOk()
            ->assertSeeText(__('author.other_pages'))
            ->assertSeeText($author->name);
    }

    public function testGroupHttp()
    {
        config(['activitylog.enabled' => true]);

        $author = Author::factory()->create();

        $another_author = Author::factory()->create();

        $admin = User::factory()->create();
        $admin->group->author_group_and_ungroup = true;
        $admin->push();

        $this->actingAs($admin)
            ->post(route('authors.group', ['author' => $author]),
                ['author' => $another_author->id])
            ->assertRedirect()
            ->assertSessionHas(['success' => __('author.group_success', ['author_name' => $author->name, 'another_author_name' => $another_author->name])]);

        $author->refresh();
        $another_author->refresh();

        $group = $author->group;

        $this->assertEquals(2, $group->count);
        $this->assertEquals($group->id, $author->group_id);
        $this->assertEquals($group->id, $another_author->group_id);
        $this->assertEquals($admin->id, $author->group_add_user);
        $this->assertEquals($admin->id, $another_author->group_add_user);

        $this->assertEquals(1, $author->activities()->count());
        $activity = $author->activities()->first();
        $this->assertEquals('group', $activity->description);
        $this->assertEquals($admin->id, $activity->causer_id);
        $this->assertEquals('user', $activity->causer_type);

        $this->assertEquals(1, $another_author->activities()->count());
        $activity = $another_author->activities()->first();
        $this->assertEquals('group', $activity->description);
        $this->assertEquals($admin->id, $activity->causer_id);
        $this->assertEquals('user', $activity->causer_type);
    }

    public function testUngroupHttp()
    {
        config(['activitylog.enabled' => true]);

        $admin = User::factory()->create();
        $admin->group->author_group_and_ungroup = true;
        $admin->push();

        $group = AuthorGroup::factory()->with_two_authors()->create();

        $author = $group->authors()->get()->first();
        $another_author = $group->authors()->get()->last();

        $this->actingAs($admin)
            ->get(route('authors.ungroup', ['author' => $another_author]))
            ->assertRedirect();

        $author->refresh();
        $another_author->refresh();
        $group->refresh();

        $this->assertEquals(1, $group->count);
        //$this->assertNull($author->group_id);
        $this->assertNull($another_author->group_id);
        $this->assertEquals($admin->id, $another_author->group_add_user);

        $this->assertEquals(1, $another_author->activities()->count());
        $activity = $another_author->activities()->first();
        $this->assertEquals('ungroup', $activity->description);
        $this->assertEquals($admin->id, $activity->causer_id);
        $this->assertEquals('user', $activity->causer_type);
    }
}
