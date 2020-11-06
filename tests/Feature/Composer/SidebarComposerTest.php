<?php

namespace Tests\Feature\Composer;

use App\User;
use App\View\Composers\SidebarComposer;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Support\ViewErrorBag;
use Illuminate\View\View;
use Tests\TestCase;

class SidebarComposerTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testTrueIfCookieShowSidebarIsTrue()
	{
		$request = \Mockery::mock(Request::class);
		$request->shouldReceive('hasCookie')->andReturn(true)
			->shouldReceive('cookie')->andReturn(true);

		$currentRoute = \Mockery::mock(Route::class);
		$currentRoute->shouldReceive('getName')->andReturn(uniqid());

		$composer = new SidebarComposer($request, $currentRoute);
		$view = \Mockery::spy(View::class);

		$composer->compose($view);

		$view->shouldHaveReceived('with')->with('showSidebar', true);
	}

	public function testFalseIfCookieShowSidebarIsFalse()
	{
		$request = \Mockery::mock(Request::class);
		$request->shouldReceive('hasCookie')->andReturn(true)
			->shouldReceive('cookie')->andReturn(false);

		$currentRoute = \Mockery::mock(Route::class);
		$currentRoute->shouldReceive('getName')->andReturn(uniqid());

		$composer = new SidebarComposer($request, $currentRoute);
		$view = \Mockery::spy(View::class);

		$composer->compose($view);

		$view->shouldHaveReceived('with')->with('showSidebar', false);
	}

	public function testTrueIfCookieIsNull()
	{
		$request = \Mockery::mock(Request::class);
		$request->shouldReceive('hasCookie')->andReturn(true)
			->shouldReceive('cookie')->andReturn(null);

		$currentRoute = \Mockery::mock(Route::class);
		$currentRoute->shouldReceive('getName')->andReturn(uniqid());

		$composer = new SidebarComposer($request, $currentRoute);
		$view = \Mockery::spy(View::class);

		$composer->compose($view);

		$view->shouldHaveReceived('with')->with('showSidebar', true);
	}

	public function testTrueIfAuthUserAndReadStyleShowSidebarTrueAndCookieSidebarFalseAndRouteIsBookPage()
	{
		$user = factory(User::class)->create();
		$user->readStyle->show_sidebar = true;
		$user->push();

		$this->be($user);

		$request = \Mockery::mock(Request::class);
		$request->shouldReceive('hasCookie')->andReturn(true)
			->shouldReceive('cookie')->andReturn(false);

		$currentRoute = \Mockery::mock(Route::class);
		$currentRoute->shouldReceive('getName')->andReturn('books.sections.show');

		$composer = new SidebarComposer($request, $currentRoute);
		$view = \Mockery::spy(View::class);

		$composer->compose($view);

		$view->shouldHaveReceived('with')->with('showSidebar', true);
	}

	public function testFalseIfAuthUserAndReadStyleShowSidebarFalseAndCookieSidebarTrue()
	{
		$user = factory(User::class)->create();
		$user->readStyle->show_sidebar = false;
		$user->push();

		$this->be($user);

		$request = \Mockery::mock(Request::class);
		$request->shouldReceive('hasCookie')->andReturn(true)
			->shouldReceive('cookie')->andReturn(true);

		$currentRoute = \Mockery::mock(Route::class);
		$currentRoute->shouldReceive('getName')->andReturn('books.sections.show');

		$composer = new SidebarComposer($request, $currentRoute);
		$view = \Mockery::spy(View::class);

		$composer->compose($view);

		$view->shouldHaveReceived('with')->with('showSidebar', false);
	}

	public function testTrueIfHasErrorsBag()
	{
		$user = factory(User::class)->create();
		$user->readStyle->show_sidebar = false;
		$user->push();

		$this->be($user);

		$request = \Mockery::mock(Request::class);
		$request->shouldReceive('hasCookie')->andReturn(true)
			->shouldReceive('cookie')->andReturn(false);

		$currentRoute = \Mockery::mock(Route::class);
		$currentRoute->shouldReceive('getName')->andReturn('books.sections.show');

		$composer = new SidebarComposer($request, $currentRoute);
		$view = \Mockery::spy(View::class);

		$error_bag = \Mockery::mock(ViewErrorBag::class);
		$error_bag->shouldReceive('hasBag')->andReturn(true);

		$view->shouldReceive('getData')
			->andReturn(['errors' => $error_bag]);

		$composer->compose($view);

		$view->shouldHaveReceived('with')->with('showSidebar', true);
	}

	public function testTrueIfCookieDoesntExists()
	{
		$request = \Mockery::mock(Request::class);
		$request->shouldReceive('hasCookie')->andReturn(false);

		$currentRoute = \Mockery::mock(Route::class);
		$currentRoute->shouldReceive('getName')->andReturn(uniqid());

		$composer = new SidebarComposer($request, $currentRoute);
		$view = \Mockery::spy(View::class);

		$composer->compose($view);

		$view->shouldHaveReceived('with')->with('showSidebar', true);
	}
}
