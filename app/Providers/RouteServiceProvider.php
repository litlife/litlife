<?php

namespace App\Providers;

use App\Author;
use App\Book;
use App\BookFile;
use App\Genre;
use App\Sequence;
use App\User;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
	/**
	 * This namespace is applied to your controller routes.
	 *
	 * In addition, it is set as the URL generator's root namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'App\Http\Controllers';

	/**
	 * Define your route model bindings, pattern filters, etc.
	 *
	 * @return void
	 */
	public function boot()
	{
		Route::pattern('book', '[0-9]{1,9}');
		Route::pattern('author', '[0-9]{1,9}');
		Route::pattern('user', '[0-9]{1,9}');
		Route::pattern('forum_group', '[0-9]{1,9}');
		Route::pattern('post', '[0-9]{1,9}');
		Route::pattern('topic', '[0-9]{1,9}');
		Route::pattern('forum', '[0-9]{1,9}');
		Route::pattern('sequence', '[0-9]{1,9}');
		Route::pattern('comment', '[0-9]{1,9}');
		Route::pattern('blog', '[0-9]{1,9}');
		Route::pattern('section', '[0-9]{1,9}');
		Route::pattern('note', '[0-9]{1,9}');
		Route::pattern('genre', '[0-9]{1,9}');
		Route::pattern('collection', '[0-9]{1,9}');
		Route::pattern('award', '[0-9]{1,9}');
		Route::pattern('genre', '[0-9]{1,9}(.*)');
		Route::pattern('complain', '[0-9]{1,9}');
		Route::pattern('support_request', '[0-9]{1,9}');

		Route::bind('genre', function ($value) {
			return Genre::whereIdWithSlug($value)->first() ?? abort(404);
		});

		Route::bind('book', function ($value) {
			return Book::any()->where('id', intval($value))->first() ?? abort(404);
		});

		Route::bind('author', function ($value) {
			return Author::any()->where('id', intval($value))->first() ?? abort(404);
		});

		Route::bind('sequence', function ($value) {
			return Sequence::any()->where('id', intval($value))->first() ?? abort(404);
		});

		Route::bind('bookFile', function ($value) {
			return BookFile::any()->where('id', intval($value))->first() ?? abort(404);
		});

		Route::bind('section', function ($value, $route) {
			if ($book = $route->parameter('book')) {
				return $book->sections()->any()->findInnerIdOrFail(intval($value)) ?? abort(404);
			}
		});
		/*
				Route::bind('note', function ($value, $route) {
					if ($book = $route->parameter('book')) {
						return $book->sections()->any()->findInnerIdOrFail($value) ?? abort(404);
					}
				});
				*/


		Route::bind('user', function ($value) {
			return User::any()->where('id', intval($value))->first() ?? abort(404);
		});

		parent::boot();
	}

	/**
	 * Define the routes for the application.
	 *
	 * @return void
	 */
	public function map()
	{
		$this->mapApiRoutes();

		$this->mapWebRoutes();

		//
	}

	/**
	 * Define the "api" routes for the application.
	 *
	 * These routes are typically stateless.
	 *
	 * @return void
	 */
	protected function mapApiRoutes()
	{
		Route::prefix('api')
			->middleware('api')
			->namespace($this->namespace)
			->group(base_path('routes/api.php'));
	}

	/**
	 * Define the "web" routes for the application.
	 *
	 * These routes all receive session state, CSRF protection, etc.
	 *
	 * @return void
	 */
	protected function mapWebRoutes()
	{
		Route::middleware('web')
			->namespace($this->namespace)
			->group(base_path('routes/web.php'));
	}
}
