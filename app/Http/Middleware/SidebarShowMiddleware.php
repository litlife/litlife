<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Support\Facades\Auth;

class SidebarShowMiddleware
{
	/**
	 * The view factory implementation.
	 *
	 * @var \Illuminate\Contracts\View\Factory
	 */
	public $view;

	public $readPagesRouteNames = ['books.old.page', 'books.sections.show'];

	/**
	 * Create a new error binder instance.
	 *
	 * @param \Illuminate\Contracts\View\Factory $view
	 * @return void
	 */
	public function __construct(ViewFactory $view)
	{
		$this->view = $view;
	}

	/**
	 * Handle an incoming request.
	 *
	 * @param \Illuminate\Http\Request $request
	 * @param \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		$showSidebar = true;

		if (Auth::check() and in_array($request->route()->getName(), $this->readPagesRouteNames)) {
			if (Auth::user()->readStyle->show_sidebar)
				$showSidebar = true;
			else
				$showSidebar = false;
		} else {

			if ($request->hasCookie('show_sidebar')) {
				$cookie = $request->cookie('show_sidebar');

				if ($cookie === null or $cookie == 'undefined') {
					$showSidebar = true;
				} else {
					if ($cookie)
						$showSidebar = true;
					else
						$showSidebar = false;
				}
			}
		}

		if (!empty($errorsBag = $this->view->shared('errors'))) {
			if ($errorsBag->hasBag('login'))
				$showSidebar = true;
		}

		$this->view->share('showSidebar', $showSidebar);

		return $next($request);
	}
}
