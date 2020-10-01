<?php

namespace App\View\Composers;

use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class SidebarComposer
{
	private $request;
	private $currentRoute;
	public $readPagesRouteNames = ['books.old.page', 'books.sections.show'];

	/**
	 * Pass $request
	 */
	public function __construct(Request $request, Route $currentRoute)
	{
		$this->request = $request;
		$this->currentRoute = $currentRoute;
	}

	/**
	 * Bind data to the view.
	 *
	 * @param View $view
	 * @return void
	 */
	public function compose(View $view)
	{
		$showSidebar = true;

		if (Auth::check() and in_array($this->currentRoute->getName(), $this->readPagesRouteNames)) {
			if (Auth::user()->readStyle->show_sidebar)
				$showSidebar = true;
			else
				$showSidebar = false;
		} else {

			if ($this->request->hasCookie('show_sidebar')) {
				$cookie = $this->request->cookie('show_sidebar');

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

		if (!empty($errorsBag = $view->getData()['errors'])) {
			if ($errorsBag->hasBag('login'))
				$showSidebar = true;
		}

		$view->with('showSidebar', $showSidebar);

		\Illuminate\Support\Facades\View::share('showSidebar', $showSidebar);
	}
}