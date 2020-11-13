<?php

namespace Tests\Browser;

use App\User;
use Tests\DuskTestCase;

class UserSearchSettingTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testFilterSettingSaved()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books'))
                ->with('.books-search-container form', function ($form) {
                    $form->assertVisible('[name="download_access"].save');

                    $form->select('download_access', 'close')
                        ->waitFor('[name="download_access"].is-valid');
                });

            $setting = $user->booksSearchSettings()->first();

            $this->assertEquals('download_access', $setting->name);
            $this->assertEquals('close', $setting->value);
        });
    }
}
