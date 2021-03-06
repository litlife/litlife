<?php

namespace App\Exceptions;

use Exception;
use Facade\Ignition\Exceptions\ViewException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Exceptions\UrlGenerationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;

class Handler extends ExceptionHandler
{
	/**
	 * A list of the exception types that should not be reported.
	 *
	 * @var array
	 */
	protected $dontReport = [
		AuthenticationException::class,
		AuthorizationException::class,
		HttpException::class,
		ModelNotFoundException::class,
		TokenMismatchException::class,
		ValidationException::class,
	];

	/**
	 * Report or log an exception.
	 *
	 * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
	 *
	 * @param Exception $exception
	 * @return void
	 */
	public function report(Throwable $exception)
	{
		parent::report($exception);
		/*
				$s = '';

				if (app()->runningInConsole())
					$s .= 'Artisan '."\n";
				else
				{
					$s .= 'URL: '.url()->full()."\n";
					$s .= 'Http '."\n";
					$s .= 'Method: '.request()->getMethod()."\n";
					$s .= 'Action: '.Route::currentRouteAction()."\n";
					$s .= 'Route: '.Route::currentRouteName()."\n";
					$s .= 'Client ip: '.request()->getClientIp()."\n";
					$s .= 'Query string: '.request()->getQueryString()."\n";
					$s .= 'Locale: '.request()->getLocale()."\n";
				}

				Log::info($s);
				*/
	}

	/**
	 * Render an exception into an HTTP response.
	 *
	 * @param Request $request
	 * @param Throwable $exception
	 * @return Response
	 */
	public function render($request, Throwable $exception)
	{
		if ($exception instanceof AuthorizationException) {

			if ($request->expectsJson()) {
				if ($exception->getMessage() == 'This action is unauthorized.')
					return response()->json(['error' => __('error.this_action_is_unauthorized')], 403);
				else
					return response()->json(['error' => $exception->getMessage()], 403);
			}

			// Redirect to error page instead
			return response()->view('errors.403', ['exception' => $exception], 403);
		}

		if ($exception instanceof AuthenticationException) {
			if ($request->expectsJson()) {
				return response()->json(['error' => __('error.401')], 401);
			}
			// Redirect to error page instead
			return response()->view('errors.401', ['exception' => $exception], 401);
		}

		if (App::environment() == 'testing') {
			//dd(config('app.debug'));

            if ($exception instanceof \Error)
                throw $exception;

			if ($exception instanceof UrlGenerationException)
                throw $exception;

            if ($exception instanceof \ErrorException) {
                throw $exception;
            }

			if ($exception instanceof QueryException) {
				throw $exception;
			}

			if ($exception instanceof ViewException) {
				throw $exception;
			}

			if ($exception instanceof \InvalidArgumentException) {
				throw $exception;
			}

			if ($exception instanceof \ArgumentCountError) {
				throw $exception;
			}

			if ($exception instanceof \BadMethodCallException)
				throw $exception;
		}

		return parent::render($request, $exception);
	}

	/**
	 * Get the default context variables for logging.
	 *
	 * @return array
	 */
	protected function context()
	{
		if (App::runningInConsole()) {
			return array_merge(parent::context(), $_SERVER['argv']);
		} else {
			return array_merge(parent::context(), [
				'url' => request()->url(),
				'values' => var_export(request()->all(), true),
			]);
		}
	}
}
