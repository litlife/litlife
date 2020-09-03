<?php

namespace Litlife\Unitpay;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class UnitPayServiceProvider extends ServiceProvider implements DeferrableProvider
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

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return [
			UnitPayApi::class,
			'unitpay'
		];
	}
}
