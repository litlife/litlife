<?php

namespace Litlife\Unitpay\Facades;

use Illuminate\Support\Facades\Facade;

class UnitPay extends Facade
{
	protected static function getFacadeAccessor()
	{
		return 'unitpay';
	}
}
