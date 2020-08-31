<?php

namespace App\Http\Middleware;

use App\Variable;
use Closure;
use Illuminate\Http\Request;

class ErrorIfForbiddenWordsExists
{
	public $forbidden_words;
	protected $exceptedRoutes = [
		'settings.index',
		'settings.save'
	];

	/**
	 * Handle an incoming request.
	 *
	 * @param Request $request
	 * @param Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{
		if ($request->method() != 'GET') {
			if (count($all = request()->all()) > 0) {
				$settings = Variable::where('name', 'settings')
					//->remember(86400)
					->first();

				$this->forbidden_words = $settings->value['forbidden_words'] ?? [];

				if (is_string($this->forbidden_words))
					$this->forbidden_words = [$this->forbidden_words];

				foreach ($this->forbidden_words as $c => $word) {
					$this->forbidden_words[$c] = preg_quote($word, '/');
				}

				if (!empty($this->forbidden_words)) {
					if (!in_array(request()->route()->getName(), $this->exceptedRoutes)) {
						$this->search($all);
					}
				}
			}
		}

		return $next($request);
	}

	protected function search($value)
	{
		if (is_array($value)) {
			$array = $value;

			foreach ($array as $value) {
				$this->search($value);
			}
		} else {
			if (preg_match('/' . implode('|', $this->forbidden_words) . '/iu', $value))
				abort(403);
		}
	}
}
