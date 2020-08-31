<?php

namespace Litlife\BookConverter;

use Illuminate\Support\ServiceProvider;

class BookConverterServiceProvider extends ServiceProvider
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
}
