<?php

namespace Litlife\Unitpay;

use Illuminate\Support\ServiceProvider;

class UnitPayServiceProvider extends ServiceProvider
{
	/**
	 * Register services.
	 *
	 * @return void
	 */
	public function register()
	{

	}

	/**
	 * Bootstrap services.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->app->bind('unitpay', function ($app) {
			return new UnitPayApi();
		});
	}
}
