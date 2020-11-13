<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;

class ConvertToUtf8MiddlewareTest extends TestCase
{
    public function testInit()
    {
        $value = 'тест';

        $url = '/?key='.urlencode(mb_convert_encoding($value, "KOI8-R", 'UTF-8'));

        $response = $this->get($url)
            ->assertOk();

        $this->assertEquals(null, request()->input('key'));
    }
}
