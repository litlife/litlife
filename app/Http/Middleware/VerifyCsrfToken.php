<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;
use Illuminate\Support\Facades\Route;

class VerifyCsrfToken extends BaseVerifier
{
	/**
	 * The URIs that should be excluded from CSRF verification.
	 *
	 * @var array
	 */
	protected $except = [
		//
	];

	public function handle($request, Closure $next)
	{
		/*
		if (request()->ajax())
			throw new TokenMismatchException;
  */
		$route = Route::getRoutes()->match($request);
		$routeAction = $route->getAction();
		if (isset($routeAction['nocsrf']) && $routeAction['nocsrf']) {
			return $next($request);
		}
		return parent::handle($request, $next);
	}
}
