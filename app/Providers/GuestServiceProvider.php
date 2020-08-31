<?php

namespace App\Providers;

use App\User;
use Auth;
use Illuminate\Support\ServiceProvider;

class GuestServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// Laravel policies only work if the user isn't null so for guest access we need to assign a dummpy user.
		// From now on to check for guest use is_null(Auth::user()->getKey())
		if (!Auth::check()) {
			Auth::setUser(new User());
		}
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register()
	{
		//
	}
}
