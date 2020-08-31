<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use URL;

class RedirectIfAuthenticated
{
	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @param string|null $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = null)
	{
		if (Auth::guard($guard)->check()) {

			if (request()->fullUrl() == URL::previous())
				return redirect()->route('profile', Auth::id());
			else
				return back();
		}

		return $next($request);
	}
}
