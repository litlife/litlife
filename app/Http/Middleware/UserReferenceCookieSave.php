<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Litlife\Url\Url;

class UserReferenceCookieSave
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
		$name = config('litlife.name_user_refrence_get_param');

		$litlifeReferenceUserId = $request->get($name);

		if (!empty($litlifeReferenceUserId)) {
			$url = Url::fromString(request()->fullUrl());

			$cookie = cookie($name, $litlifeReferenceUserId, now()->diffInMinutes(now()->addMonth()));

			return redirect()
				->to($url->withoutQueryParameter($name))
				->cookie($cookie);
		}

		return $next($request);
	}
}
