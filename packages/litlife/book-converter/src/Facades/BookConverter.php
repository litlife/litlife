<?php

namespace Litlife\BookConverter\Facades;

use Illuminate\Support\Facades\Facade;

class BookConverter extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'book_converter';
	}
}
