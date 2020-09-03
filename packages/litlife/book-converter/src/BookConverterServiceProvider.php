<?php

namespace Litlife\BookConverter;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class BookConverterServiceProvider extends ServiceProvider implements DeferrableProvider
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
		$this->app->bind('book_converter', function ($app) {
			return new BookConverter();
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
			BookConverter::class,
			'book_converter'
		];
	}
}
