<?php

namespace App\Http\Middleware;

use App\UserGroup;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
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
		$group = UserGroup::findOrFail(config('litlife.admin_group_id'));

		if (Auth::guard($guard)->user()->hasUserGroup($group))
			return $next($request);
		else
			abort(403);
	}
}
