<?php

namespace App\Http\Middleware;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
	/**
	 * Determine if the user is logged in to any of the given guards.
	 *
	 * @param Request $request
	 * @param array $guards
	 * @return void
	 *
	 * @throws AuthenticationException
	 */
	protected function authenticate($request, array $guards)
	{
		if (empty($guards)) {
			$guards = [null];
		}

		foreach ($guards as $guard) {
			if ($this->auth->guard($guard)->check()) {
				return $this->auth->shouldUse($guard);
			}
		}

		throw new AuthenticationException(
			__('user.unauthenticated_error_description', ['url' => route('invitation')]), $guards, $this->redirectTo($request)
		);
	}

	/**
	 * Get the path the user should be redirected to when they are not authenticated.
	 *
	 * @param Request $request
	 * @return string
	 */
	protected function redirectTo($request)
	{
		if (!$request->expectsJson()) {
			return route('login');
		}
	}
}