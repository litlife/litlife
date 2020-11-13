<?php

namespace Tests\Feature\Artisan;

use App\Comment;
use App\User;
use App\UserGroup;
use Tests\TestCase;

class AttachCommentMasterGroupToUsersTest extends TestCase
{
    public function testDetachActiveCommentatorGroup()
    {
        $this->assertGroupExists();

        $text = $this->faker->realText(200);

        $user = User::factory()->create();

        $user->attachUserGroupByNameIfExists('Активный комментатор');

        $comments = Comment::factory()
            ->count(2)
            ->create(['create_user_id' => $user->id, 'bb_text' => $text]);

        $this->assertUserDontHasGroup($user);
        $this->assertEquals(1, $user->groups()->disableCache()->whereName('Активный комментатор')->count());

        $this->artisan('user:attach_comment_master_group_to_users', [
            'min_characters_count' => 100,
            'min_comments_count' => 2,
            'id' => $user->id
        ]);

        $this->assertUserHasGroup($user);
        $this->assertEquals(0, $user->groups()->disableCache()->whereName('Активный комментатор')->count());
    }

    public function assertGroupExists()
    {
        $group = UserGroup::whereName('Мастер комментария')->first();
        $this->assertNotNull($group);
        return $group;
    }

    public function assertUserDontHasGroup($user)
    {
        $this->assertEquals(0, $user->groups()->disableCache()->whereName('Мастер комментария')->count());
    }

    public function assertUserHasGroup($user)
    {
        $this->assertEquals(1, $user->groups()->disableCache()->whereName('Мастер комментария')->count());
    }

    public function testWhereHasQuery()
    {
        $group = $this->assertGroupExists();

        $user = User::factory()->create();

        $text = $this->faker->realText(200);

        $comments = Comment::factory()
            ->count(2)
            ->create(['create_user_id' => $user->id, 'bb_text' => $text]);

        $this->artisan('user:attach_comment_master_group_to_users', [
            'min_characters_count' => 100,
            'min_comments_count' => 2,
            'id' => $user->id
        ])
            ->expectsOutput('Пользователь: '.$user->id.'')
            ->expectsOutput('Присоединяем группу '.$group->name.' к пользователю '.$user->id.'')
            ->assertExitCode(0);
    }

    public function testDontAttachIfConditionsDidntMatch()
    {
        $this->assertGroupExists();

        $user = User::factory()->create();

        $text = $this->faker->realText(200);

        $comments = Comment::factory()
            ->count(2)
            ->create(['create_user_id' => $user->id, 'bb_text' => $text]);

        $this->assertUserDontHasGroup($user);

        $this->artisan('user:attach_comment_master_group_to_users', [
            'min_characters_count' => 100,
            'min_comments_count' => 3,
            'id' => $user->id
        ]);

        $this->assertUserDontHasGroup($user);
    }
}
