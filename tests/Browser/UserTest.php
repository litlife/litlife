<?php

namespace Tests\Browser;

use App\User;
use App\UserData;
use App\UserEmail;
use Illuminate\Support\Facades\Storage;
use Litlife\Url\Url;
use Tests\DuskTestCase;

class UserTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testSuspendAndUnsuspendAccount()
    {
        $this->browse(function ($admin_browser, $second_browser) {

            $admin_user = User::factory()->create();
            $admin_user->group->user_suspend = true;
            $admin_user->group->save();

            $second_user = User::factory()->create();
            $second_user_email = UserEmail::factory()->create([
                'user_id' => $second_user->id,
                'confirm' => true
            ]);

            $admin_browser->resize(1000, 1000)
                ->loginAs($admin_user);

            // suspend
            $admin_browser->visit(route('profile', $second_user))
                ->click('main .dropdown')
                ->assertSee(mb_strtolower(__('user.suspend')))
                ->clickLink(__('user.suspend'))
                ->click('main .dropdown')
                ->assertDontSee(mb_strtolower(__('user.suspend')))
                ->assertSee(mb_strtolower(__('user.unsuspend')))
                ->assertSee(__('user.suspended'));

            $this->assertTrue($second_user->fresh()->isSuspended());

            // try login if suspended
            $second_browser->visit('/')
                ->with('#sidebar', function ($sidebar) use ($second_user, $second_user_email) {
                    $sidebar->type('login', $second_user_email->email)
                        ->type('login_password', $second_user->password)
                        ->press(__('auth.enter'))
                        ->assertSee(__('auth.you_account_suspended_try_recover_password'));
                });

            // unsuspend
            $admin_browser->visit(route('profile', $second_user))
                ->click('main .dropdown')
                ->assertSee(mb_strtolower(__('user.unsuspend')))
                ->clickLink(__('user.unsuspend'))
                ->click('main .dropdown')
                ->assertDontSee(mb_strtolower(__('user.unsuspend')))
                ->assertSee(mb_strtolower(__('user.suspend')));
        });
    }

    public function testDeleteAndRestore()
    {
        $this->browse(function ($admin_browser, $second_browser) {

            $admin_user = User::factory()->create();
            $admin_user->group->user_delete = true;
            $admin_user->group->save();

            $second_user = User::factory()->create();
            $second_user_email = UserEmail::factory()->create([
                'user_id' => $second_user->id,
                'confirm' => true
            ]);

            $admin_browser->resize(1000, 1000)
                ->loginAs($admin_user);

            // delete
            $admin_browser->visit(route('profile', $second_user))
                ->click('main .dropdown')
                ->assertSee(mb_strtolower(__('user.delete')))
                ->clickLink(__('user.delete'))
                ->assertSee(__('user.deleted'))
                ->click('main .dropdown')
                ->assertDontSee(mb_strtolower(__('user.delete')))
                ->assertSee(mb_strtolower(__('common.restore')));

            // try login if deleted
            $second_browser->visit('/')
                ->with('#sidebar', function ($sidebar) use ($second_user, $second_user_email) {
                    $sidebar->type('login', $second_user_email->email)
                        ->type('login_password', $second_user->password)
                        ->press(__('auth.enter'))
                        ->assertSee(__('user.deleted'));
                });

            // restore
            $admin_browser->visit(route('profile', $second_user))
                ->click('main .dropdown')
                ->assertSee(mb_strtolower(__('common.restore')))
                ->clickLink(__('common.restore'))
                ->click('main .dropdown')
                ->assertDontSee(mb_strtolower(__('common.restore')))
                ->assertSee(mb_strtolower(__('user.delete')));

        });
    }

    public function testEditProfile()
    {
        $this->browse(function ($user_browser) {

            $user = User::factory()->create();
            $user->group->edit_profile = true;
            $user->push();

            $user_email = UserEmail::factory()
                ->create([
                    'user_id' => $user->id,
                    'confirm' => true
                ]);

            $new_user = User::factory()
                ->make();

            $new_user_data = UserData::factory()
                ->make();

            $user_browser->resize(1200, 2000)
                ->loginAs($user);

            $user_browser->visit(route('profile', $user))
                ->click('main .dropdown')
                ->assertSee(mb_strtolower(__('user.edit_profile')))
                ->clickLink(__('user.edit_profile'));

            $user_browser->type('nick', $new_user->nick)
                ->type('first_name', $new_user->first_name)
                ->type('last_name', $new_user->last_name)
                ->select('gender', $new_user->gender)
                ->type('data[about_self]', $new_user_data->about_self)
                ->press(__('common.save'))
                ->assertSee(__('user.profile_edit_success'));
        });
    }

    public function testUploadDeleteAvatar()
    {
        $this->browse(function ($user_browser) {

            $user = User::factory()->create();
            $user->group->edit_profile = true;
            $user->push();

            $user_browser->resize(1000, 1000)
                ->loginAs($user);

            // upload avatar

            $user_browser->visit(route('users.edit', $user))
                ->attach('file', __DIR__.'/images/test.jpeg')
                ->press(__('common.upload'))
                ->assertSee(__('user_photo.upload_success'));

            $avatar = User::findOrFail($user->id)->avatar;

            $this->assertTrue(is_object($avatar));

            Storage::disk('public')->assertExists($avatar->dirname.'/'.$avatar->name);

            // delete avatar

            $user_browser->visit(route('users.edit', $user))
                ->assertSee(__('common.delete'))
                ->clickLink(__('common.delete'))
                ->assertSee(__('user_photo.deleted'));

            //Storage::disk('public')->assertMissing($avatar->dirname.'/'.$avatar->name);

            $this->assertTrue(!is_object(User::findOrFail($user->id)->avatar));
        });
    }

    public function testToggleSidebar()
    {
        $this->browse(function ($user_browser) {

            $user = User::factory()->create();

            $user_browser->resize(1000, 1000)
                ->loginAs($user);

            $user_browser->visit(route('profile', $user))
                ->assertVisible('#sidebar')
                ->click('#sidebar-toggle')
                ->waitUntilMissing('#sidebar')
                ->assertMissing('#sidebar')
                ->pause(3000)
                ->visit(route('profile', $user))
                ->assertMissing('#sidebar', 15)
                ->click('#sidebar-toggle')
                ->waitFor('#sidebar')
                ->assertVisible('#sidebar');
        });
    }

    public function testDontSeeAdministrativeFunctions()
    {
        $this->browse(function ($user_browser) {

            $user = User::factory()->with_user_group()->create();

            $user_browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('profile', $user))
                ->assertVisible('#sidebar')
                ->with('#sidebar', function ($sidebar) {
                    $sidebar->assertDontSee(__('navbar.admin_functions'));
                });
        });
    }

    public function testReferUrlGenerator()
    {
        $this->browse(function ($user_browser) {

            $user = User::factory()->create();

            $user_browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('users.refer'))
                ->assertVisible('.url-maker')
                ->value('.url-maker', route('profile', ['user' => $user, 'key' => 'value']))
                ->append('.url-maker', '  ');

            $url = $user_browser
                ->pause(500)
                ->value('.url-maker');

            $referUrl = (string) Url::fromString(route('profile', $user))
                ->withScheme('https')
                ->withQueryParameter('key', 'value')
                ->withQueryParameter(config('litlife.name_user_refrence_get_param'), $user->id);

            $this->assertEquals($referUrl, $url);
        });
    }
}
