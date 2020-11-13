<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\RemeberSessionGeoIpAndBrowser;
use Illuminate\Http\Request;
use Tests\TestCase;

class RemeberSessionGeoIpAndBrowserTest extends TestCase
{
    public function test()
    {
        $ip = $this->faker->ipv4;
        $userAgent = 'Mozilla/5.0 (Linux; Android 6.0; LG-K430) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.101 Mobile Safari/537.36';

        $response = $this->withMiddleware(RemeberSessionGeoIpAndBrowser::class)
            ->get('/', [
                'REMOTE_ADDR' => $ip,
                'user-agent' => $userAgent
            ])->assertOk();

        $response->assertSessionHas('geoip')
            ->assertSessionHas('browser');
    }

    public function testEmpty()
    {
        $ip = '';
        $userAgent = '';

        $response = $this->withMiddleware(RemeberSessionGeoIpAndBrowser::class)
            ->get('/', [
                'REMOTE_ADDR' => $ip,
                'user-agent' => $userAgent
            ])->assertOk();

        $response->assertSessionHas('geoip')
            ->assertSessionHas('browser');
    }
}
