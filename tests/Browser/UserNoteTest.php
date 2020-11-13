<?php

namespace Tests\Browser;

use App\User;
use App\UserNote;
use Tests\DuskTestCase;

class UserNoteTest extends DuskTestCase
{


    public function testCreate()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $text = $this->faker->realText(300);

            // create
            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('users.notes.index', $user))
                ->clickLink(__('common.create'));

            $browser->driver->executeScript('sceditor.instance(document.getElementById("bb_text")).insertText("'.$text.'");');

            $browser->press(__('common.create'))
                ->assertSee($text);
        });
    }

    public function testEdit()
    {
        $this->browse(function ($browser) {

            $note = UserNote::factory()->create();

            $user = $note->create_user;

            $text = $this->faker->realText(300);

            // create
            $browser->resize(1000, 1000)
                ->loginAs($note->create_user)
                ->visit(route('users.notes.index', $note->create_user))
                ->with('[data-id="'.$note->id.'"]', function ($item) {
                    $item->press('[data-toggle="dropdown"]')
                        ->whenAvailable('.dropdown-menu.show', function ($menu) {
                            $menu->assertSee(mb_strtolower(__('common.edit')))
                                ->clickLink(mb_strtolower(__('common.edit')));
                        });
                });

            $browser->driver
                ->executeScript('sceditor.instance(document.getElementById("bb_text")).insertText("'.$text.'");');

            $browser->press(__('common.save'))
                ->assertSee($text);
        });
    }

    public function testDelete()
    {
        $this->browse(function ($browser) {

            $note = UserNote::factory()->create();

            $user = $note->create_user;

            $text = $this->faker->realText(300);

            // create
            $browser->resize(1000, 1000)
                ->loginAs($note->create_user)
                ->visit(route('users.notes.index', $note->create_user))
                ->with('[data-id="'.$note->id.'"]', function ($item) {
                    $item->press('[data-toggle="dropdown"]')
                        ->whenAvailable('.dropdown-menu.show', function ($menu) {
                            $menu->assertSee(mb_strtolower(__('common.delete')))
                                ->click('.delete');
                        });
                })
                ->waitFor('[data-id="'.$note->id.'"].transparency');

            $user->refresh();

            $this->assertEquals(0, $user->notes()->count());

            $browser->with('[data-id="'.$note->id.'"]', function ($item) {
                $item->press('[data-toggle="dropdown"]')
                    ->whenAvailable('.dropdown-menu.show', function ($menu) {
                        $menu->assertSee(mb_strtolower(__('common.restore')))
                            ->click('.restore');
                    });
            })
                ->visit(route('users.notes.index', $note->create_user));

            $user->refresh();

            $this->assertEquals(1, $user->notes()->count());
        });
    }

}
