<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Dusk\Browser;

class DuskBrowserServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		if ($this->app->isLocal()) {
			Browser::macro('switchFrame', function ($frame = null) {
				if ($frame) {
					$this->driver->switchTo()->defaultContent()->switchTo()->frame($this->resolver->findOrFail($frame));
				} else {
					// Main frame
					$this->driver->switchTo()->defaultContent();
				}
				return $this;
			});

			Browser::macro('scrollToElement', function ($element = null) {
				$this->script("$('html, body').animate({ scrollTop: $('$element').offset().top }, 0);");

				return $this;
			});
		}


	}

	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{

	}
}
