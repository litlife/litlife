<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RefreshUserLastActivity
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
		if (auth()->check())
			auth()->user()->update_activity();

		return $next($request);
	}
}
