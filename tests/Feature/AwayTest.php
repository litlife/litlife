<?php

namespace Tests\Feature;

use Litlife\Url\Url;
use Tests\TestCase;

class AwayTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testParam()
    {
        $url = 'http://test.test/test';

        $this->get(route('away', ['url' => $url]))
            ->assertOk();

        $this->get(route('away', ['url' => '']))
            ->assertRedirect(route('home'));

        $this->get(route('away'))
            ->assertRedirect(route('home'));

        $url = 'test';

        $this->get(route('away', ['url' => $url]))
            ->assertRedirect('/'.$url);

        $url = 'test&#^@#%#@%&!@!';

        $this->get(route('away', ['url' => $url]))
            ->assertRedirect('/'.$url);
    }

    public function testWhiteList()
    {
        $url = 'http://notwhitelisted.com/test';

        $url = Url::fromString($url);

        $this->get(route('away', ['url' => (string) $url]))
            ->assertOk()
            ->assertSeeText(__('common.away_warning', ['host' => $url->getHost(), 'url' => (string) $url]))
            ->assertSeeText(__('common.away_warning_button_text'));

        config(['away.whitelist_hosts' => ['facebook.com']]);
        $url = 'http://facebook.com/test';
        $url = Url::fromString($url);

        $this->get(route('away', ['url' => (string) $url]))
            ->assertRedirect((string) $url);

        $url = config('app.url').'/test';
        $url = Url::fromString($url);
        $this->get(route('away', ['url' => (string) $url]))
            ->assertRedirect((string) $url);
    }

    public function testQueryParams()
    {
        $url = 'http://test.test/test?test=test&test2=test2#test';

        $this->get(route('away', ['url' => $url]))
            ->assertOk()
            ->assertViewHas('url', $url)
            ->assertViewHas('host', parse_url($url, PHP_URL_HOST));

        config(['away.whitelist_hosts' => ['test.test']]);

        $this->get(route('away', ['url' => $url]))
            ->assertRedirect((string) $url);
    }

    public function testWrongUrl()
    {
        $url = 'testhttps://example.com';

        $this->get(route('away', ['url' => $url]))
            ->assertRedirect(route('home'));
    }

    public function testLocal()
    {
        $url = 'file.txt';

        $this->get(route('away', ['url' => $url]))
            ->assertRedirect('file.txt');
    }
}
