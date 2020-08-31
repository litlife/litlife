<?php

namespace App\Http\Middleware;

use Browser;
use Closure;
use Illuminate\Http\Request;
use MaxMind\Db\Reader\InvalidDatabaseException;

class RemeberSessionGeoIpAndBrowser
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$ip = $request->ip();
		$geoip = $request->session()->get('geoip');

		if (empty($geoip) or empty($geoip->ip) or $ip != $geoip->ip) {
			try {
				$request->session()->put('geoip', geoip()->getLocation($ip));
			} catch (InvalidDatabaseException $exception) {

			}
		}

		$browser = $request->session()->get('browser');

		if (empty($browser))
			$request->session()->put('browser', Browser::detect());

		return $next($request);
	}
}
