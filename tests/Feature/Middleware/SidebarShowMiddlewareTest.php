<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\SidebarShowMiddleware;
use App\User;
use Illuminate\Contracts\View\Factory as ViewFactory;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\ViewErrorBag;
use Tests\TestCase;

class SidebarShowMiddlewareTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testTrueIfCookieShowSidebarIsTrue()
	{
		$request = \Mockery::mock(Request::class);

		$request->shouldReceive('hasCookie')
			->andReturn(true)
			->shouldReceive('cookie')
			->andReturn(true);

		$view = \Mockery::spy(ViewFactory::class);

		$middleware = new SidebarShowMiddleware($view);

		$middleware->handle($request, function ($req) {
		});

		$view->shouldHaveReceived('share')->with('showSidebar', true);
	}

	public function testFalseIfCookieShowSidebarIsFalse()
	{
		$request = \Mockery::mock(Request::class);

		$request->shouldReceive('hasCookie')
			->andReturn(true)
			->shouldReceive('cookie')
			->andReturn(false);

		$view = \Mockery::spy(ViewFactory::class);

		$middleware = new SidebarShowMiddleware($view);

		$middleware->handle($request, function ($req) {
		});

		$view->shouldHaveReceived('share')->with('showSidebar', false);
	}

	public function testTrueIfCookieIsNull()
	{
		$request = \Mockery::mock(Request::class);

		$request->shouldReceive('hasCookie')
			->andReturn(true)
			->shouldReceive('cookie')
			->andReturn(null);

		$currentRoute = \Mockery::mock(Route::class);

		$currentRoute->shouldReceive('getName')
			->andReturn(uniqid());

		$request->shouldReceive('route')
			->andReturn($currentRoute);

		$view = \Mockery::spy(ViewFactory::class);

		$middleware = new SidebarShowMiddleware($view);

		$middleware->handle($request, function ($req) {
		});

		$view->shouldHaveReceived('share')->with('showSidebar', true);
	}

	public function testTrueIfAuthUserAndReadStyleShowSidebarTrueAndCookieSidebarFalseAndRouteIsBookPage()
	{
		$user = User::factory()->create();
		$user->readStyle->show_sidebar = true;
		$user->push();

		$this->be($user);

		$request = \Mockery::mock(Request::class);

		$request->shouldReceive('hasCookie')
			->andReturn(true)
			->shouldReceive('cookie')
			->andReturn(false);

		$currentRoute = \Mockery::mock(Route::class);

		$currentRoute->shouldReceive('getName')
			->andReturn('books.sections.show');

		$request->shouldReceive('route')
			->andReturn($currentRoute);

		$view = \Mockery::spy(ViewFactory::class);

		$middleware = new SidebarShowMiddleware($view);

		$middleware->handle($request, function ($req) {
		});

		$view->shouldHaveReceived('share')->with('showSidebar', true);
	}

	public function testFalseIfAuthUserAndReadStyleShowSidebarFalseAndCookieSidebarTrue()
	{
		$user = User::factory()->create();
		$user->readStyle->show_sidebar = false;
		$user->push();

		$this->be($user);

		$request = \Mockery::mock(Request::class);

		$request->shouldReceive('hasCookie')
			->andReturn(true)
			->shouldReceive('cookie')
			->andReturn(true);

		$currentRoute = \Mockery::mock(Route::class);

		$currentRoute->shouldReceive('getName')
			->andReturn('books.sections.show');

		$request->shouldReceive('route')
			->andReturn($currentRoute);

		$view = \Mockery::spy(ViewFactory::class);

		$middleware = new SidebarShowMiddleware($view);

		$middleware->handle($request, function ($req) {
		});

		$view->shouldHaveReceived('share')->with('showSidebar', false);
	}

	public function testTrueIfHasErrorsBag()
	{
		$user = User::factory()->create();
		$user->readStyle->show_sidebar = false;
		$user->push();

		$this->be($user);

		$request = \Mockery::mock(Request::class);

		$request->shouldReceive('hasCookie')
			->andReturn(true)
			->shouldReceive('cookie')
			->andReturn(false);

		$currentRoute = \Mockery::mock(Route::class);

		$currentRoute->shouldReceive('getName')
			->andReturn('books.sections.show');

		$request->shouldReceive('route')
			->andReturn($currentRoute);

		$view = \Mockery::spy(ViewFactory::class);

		$errorBag = \Mockery::mock(ViewErrorBag::class);

		$errorBag->shouldReceive('hasBag')->andReturn(true);

		$view->shouldReceive('shared')
			->with('errors')
			->andReturn($errorBag);

		$middleware = new SidebarShowMiddleware($view);

		$middleware->handle($request, function ($req) {
		});

		$view->shouldHaveReceived('share')->with('showSidebar', true);
	}

	public function testTrueIfCookieDoesntExists()
	{
		$request = \Mockery::mock(Request::class);

		$request->shouldReceive('hasCookie')
			->andReturn(false);

		$currentRoute = \Mockery::mock(Route::class);

		$currentRoute->shouldReceive('getName')
			->andReturn(uniqid());

		$request->shouldReceive('route')
			->andReturn($currentRoute);

		$view = \Mockery::spy(ViewFactory::class);

		$middleware = new SidebarShowMiddleware($view);

		$middleware->handle($request, function ($req) {
		});

		$view->shouldHaveReceived('share')->with('showSidebar', true);
	}
}