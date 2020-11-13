<?php

namespace Tests\Feature\User\Email;

use App\User;
use Tests\TestCase;

class UserEmailShowInProfileTest extends TestCase
{
    public function testEnable()
    {
        $user = User::factory()->with_confirmed_email()->create();

        $email = $user->emails()->first();
        $email->show_in_profile = false;
        $email->save();

        $this->assertFalse($email->isShowInProfile());

        $this->actingAs($user)
            ->followingRedirects()
            ->get(route('users.emails.show', ['user' => $user, 'email' => $email->id]))
            ->assertSeeText(__('user_email.now_showed_in_profile', ['email' => $email->email]));

        $this->assertTrue($email->fresh()->isShowInProfile());
    }

    public function testDisable()
    {
        $user = User::factory()->with_confirmed_email()->create();

        $email = $user->emails()->first();

        $this->assertTrue($email->isShowInProfile());

        $this->actingAs($user)
            ->followingRedirects()
            ->get(route('users.emails.hide', ['user' => $user, 'email' => $email->id]))
            ->assertSeeText(__('user_email.now_not_showed_in_profile', ['email' => $email->email]));

        $this->assertFalse($email->fresh()->isShowInProfile());
    }

    public function testEnableErrorNotConfirmed()
    {
        $user = User::factory()->with_not_confirmed_email()->create();

        $email = $user->emails()->first();
        $email->show_in_profile = false;
        $email->confirm = false;
        $email->save();

        $this->assertFalse($email->isShowInProfile());

        $this->actingAs($user)
            ->followingRedirects()
            ->get(route('users.emails.show', ['user' => $user, 'email' => $email->id]))
            ->assertSeeText(__('user_email.not_confirmed'));

        $this->assertFalse($email->fresh()->isShowInProfile());
    }
}
