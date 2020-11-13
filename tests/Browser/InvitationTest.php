<?php

namespace Tests\Browser;

use App\Invitation;
use App\User;
use Illuminate\Support\Str;
use Tests\DuskTestCase;

class InvitationTest extends DuskTestCase
{
    public function testNickIsBusy()
    {
        $this->browse(function ($browser) {

            $nick = Str::random(8);

            $user = User::factory()->create(['nick' => $nick]);

            $invitation = Invitation::factory()->create();

            $browser->resize(1000, 1000)
                ->visit(route('users.registration', ['token' => $invitation->token]))
                ->assertVisible('[name="nick"]')
                ->type('nick', $nick)
                ->waitForText(__('validation.user_nick_unique'), 10)
                ->assertDontSee(__('user.this_nick_is_not_busy'))
                ->assertPresent('.is-invalid[name="nick"]');
        });
    }

    public function testNickIsFree()
    {
        $this->browse(function ($browser) {

            $nick = Str::random(8);

            $invitation = Invitation::factory()->create();

            $browser->resize(1000, 1000)
                ->visit(route('users.registration', ['token' => $invitation->token]))
                ->assertVisible('[name="nick"]')
                ->type('nick', $nick)
                ->waitForText(__('user.this_nick_is_not_busy'), 10)
                ->assertDontSee(__('validation.user_nick_unique'))
                ->assertPresent('.is-valid[name="nick"]');
        });
    }
}
