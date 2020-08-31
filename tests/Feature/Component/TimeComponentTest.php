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
		Session::put('geoip', (object)['timezone' => 'Europe/Moscow']);

		$component = new Time(Carbon::parse('2015-06-02 10:23:18'));

		$this->assertEquals('<span data-toggle="tooltip" data-placement="top" style="cursor:pointer" title="5 лет назад. Вторник">2 июня 2015 13:23</span>',
			$component->render());
	}

	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testAutoParseCarbon()
	{
		Session::put('geoip', (object)['timezone' => 'Europe/Moscow']);

		$component = new Time('2015-06-02 10:23:18');

		$this->assertEquals('<span data-toggle="tooltip" data-placement="top" style="cursor:pointer" title="5 лет назад. Вторник">2 июня 2015 13:23</span>',
			$component->render());
	}
}
