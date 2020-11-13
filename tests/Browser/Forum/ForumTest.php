<?php

namespace Tests\Browser\Forum;

use Tests\DuskTestCase;

class ForumTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    public function testChangeForumGroupOrder()
    {
        // TODO

        $this->assertTrue(true);

        /*
        $this->browse(function ($user_browser) {

            //$admin_user = User::factory()->create();

            $user = User::factory()->create();
            $user->group->forum_group_handle = true;
            $user->push();

            $forum_group = ForumGroup::factory()->create();

            $user_browser->resize(1000, 2000)
                ->loginAs($user)
                ->visit('forums.index')
                ->with('.forum_group[data-id="'.$forum_group->id.'"]', function ($forum_group) {

                    $forum_group->assertVisible('.move_group');



                });






            $forum_group->delete();
        });
        */
    }
}
