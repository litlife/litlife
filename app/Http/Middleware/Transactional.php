<?php

namespace App\Http\Middleware;

use Closure;
use DB;

class Transactional
{
	public function handle($request, Closure $next)
	{
		DB::beginTransaction();
		$response = $next($request);
		if ($response->exception) {
			DB::rollBack();
		} else {
			DB::commit();
		}
		return $response;
	}
}