<?php

namespace Tests\Feature\User\Email;

use App\UserEmail;
use Tests\TestCase;

class UserEmailIsValidMethodTest extends TestCase
{
    public function testValid()
    {
        $mail = new UserEmail();
        $mail->email = 'test@test.com';

        $this->assertTrue($mail->isValid());
    }

    public function testIsInvalid()
    {
        $mail = new UserEmail();
        $mail->email = 'test.@test.com';

        $this->assertFalse($mail->isValid());
    }
}
