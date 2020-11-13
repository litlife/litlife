<?php

namespace Tests\Feature\User\Email;

use App\UserEmail;
use Tests\TestCase;

class UserEmailConfirmEmailTest extends TestCase
{
    public function test()
    {
        $email = UserEmail::factory()->not_confirmed()->create();

        $email2 = UserEmail::factory()->confirmed()->create(['email' => $email->email]);

        $email2->user->setting->loginWithIdDisable();
        $email2->push();

        $email->confirmEmail();

        $email->refresh();
        $email2->refresh();

        $this->assertTrue($email->confirm);
        $this->assertFalse($email2->confirm);
        $this->assertEquals(1, $email->user->confirmed_mailbox_count);
        $this->assertEquals(0, $email2->user->confirmed_mailbox_count);

        $this->assertTrue($email2->user->setting->isLoginWithIdEnable());
    }
}
