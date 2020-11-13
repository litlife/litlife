<?php

namespace Tests\Browser;

use App\Blog;
use App\User;
use Faker\Factory as Faker;
use Tests\DuskTestCase;

class BlogTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */

    public function testNews()
    {
        $this->browse(function ($first, $second) {

            $first_user = User::factory()->create();
            $first_user->group->blog = true;
            $first_user->push();

            $second_user = User::factory()->create();
            $second_user->group->blog = true;
            $second_user->push();

            $first->resize(1200, 2080);
            $second->resize(1200, 2080);

            $first->loginAs($first_user)
                ->visit(route('news'))
                ->waitForText(__('navbar.news'))
                ->visit(route('profile', $first_user));

            $second->loginAs($second_user)
                ->visit(route('news'))
                ->waitForText(__('navbar.news'));

            // subscribe first user to second

            $first->visit(route('profile', $second_user))
                ->assertSee(__('common.subscribe'))
                ->clickLink(__('common.subscribe'))
                ->assertSee(__('common.unsubscribe'));

            // second user write blog post on the wall

            $second->visit(route('profile', $second_user));
            $second->driver->executeScript('sceditor.instance(document.getElementById("bb_text")).insertText("'.Faker::create()->text.'");');
            $second->press(__('common.create'));

            // assert counter is change

            $first->visit(route('profile', $first_user))
                ->with("a[href='".route('news')."'] .badge", function ($list_group_item) {
                    $list_group_item->assertSee('1');
                });

            // watch news

            $first->visit(route('news'));


            $first_user->forceDelete();
            $second_user->forceDelete();
        });
    }

    public function testChildPostDeletedWithParentPost()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $blog = Blog::factory()->create(['blog_user_id' => $user->id]);

            $blog2 = Blog::factory()->create(['blog_user_id' => $user->id, 'parent' => $blog->id]);

            $blog3 = Blog::factory()->create(['blog_user_id' => $user->id, 'parent' => $blog2->id]);

            $browser->loginAs($user)
                ->visit(route('profile', ['user' => $user]))
                ->click('.open_descendants')
                ->waitFor('.item[data-id="'.$blog2->id.'"]')
                ->waitFor('.item[data-id="'.$blog3->id.'"]')
                ->whenAvailable('.item[data-id="'.$blog->id.'"]', function ($block) {
                    $block->assertVisible('.dropdown-toggle')
                        ->click('.dropdown-toggle')
                        ->whenAvailable('.dropdown-menu.show', function ($menu) {
                            $menu->click('.delete')
                                ->waitUntilMissing('.delete');
                        })
                        ->click('.dropdown-toggle')
                        ->whenAvailable('.dropdown-menu.show', function ($menu) {
                            $menu->waitFor('.restore')
                                ->assertVisible('.restore');
                        })
                        ->assertMissing('.open_descendants')
                        ->assertMissing('.close_descendants');
                })
                ->assertMissing('.item[data-id="'.$blog2->id.'"]')
                ->assertMissing('.item[data-id="'.$blog3->id.'"]');
        });


    }
}
