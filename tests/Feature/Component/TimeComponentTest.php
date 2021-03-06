<?php

namespace Tests\Feature\Component;

use App\View\Components\Time;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class TimeComponentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testEmpty()
    {
        $component = new Time(null);

        $this->assertEquals('', $component->render());
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testTime()
    {
        Carbon::setTestNow(Carbon::parse('2020-08-02 10:23:18'));

        Session::put('geoip', (object) ['timezone' => 'Europe/Moscow']);

        $component = new Time(Carbon::parse('2015-06-02 10:23:18'));

        $this->assertEquals('<span data-toggle="tooltip" data-placement="top" style="cursor:pointer" title="{{ $title }}">{{ $text }}</span>',
            $component->render());

        $this->assertEquals('5 лет назад. Вторник', $component->title);
        $this->assertEquals('2 июня 2015 13:23', $component->text);
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testAutoParseCarbon()
    {
        Carbon::setTestNow(Carbon::parse('2020-08-02 10:23:18'));

        Session::put('geoip', (object) ['timezone' => 'Europe/Moscow']);

        $component = new Time('2015-06-02 10:23:18');

        $this->assertEquals('<span data-toggle="tooltip" data-placement="top" style="cursor:pointer" title="{{ $title }}">{{ $text }}</span>',
            $component->render());

        $this->assertEquals('5 лет назад. Вторник', $component->title);
        $this->assertEquals('2 июня 2015 13:23', $component->text);
    }
}
