<?php

namespace Tests\Feature\User;

use App\UserAgent;
use Browser;
use Tests\TestCase;

class UserAgentTest extends TestCase
{
    public function testGetCurrentId()
    {
        UserAgent::where('value', Browser::userAgent())
            ->delete();

        $this->assertNull(UserAgent::where('value', Browser::userAgent())->first());

        $agent = UserAgent::getCurrentId();

        $this->assertTrue(is_numeric($agent));

        $this->assertNotNull(UserAgent::where('value', Browser::userAgent())->first());
    }

    public function testOverflow()
    {
        $s = "Mozilla/5.0 (Linux; Android 9; SM-A505FN Build/PPR1.".uniqid()."; wv) AppleWebKit/537.36 (KHTML, like Gecko) Version/4.0 Chrome/77.0.3865.92 Mobile Safari/537.36 Instagram 123.0.0.21.114 Android (28/9; 420dpi; 1080x2218; samsung; SM-A505FN; a50; exynos9610; ru_RU; 188791674)";

        $userAgent = new UserAgent();
        $userAgent->value = $s;
        $userAgent->save();
        $userAgent->refresh();

        $this->assertEquals(mb_substr($s, 0, 255), $userAgent->value);
        $this->assertEquals('Instagram App 123', $userAgent->parsed->browserName());
    }
}
