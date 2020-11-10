<?php

namespace Tests\Feature\Middleware;

use App\Http\Middleware\AdminMiddleware;
use App\User;
use App\UserGroup;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
	/** @test */
	public function non_admins_are_redirected()
	{
		$group = UserGroup::factory()->create();

		config(['litlife.admin_group_id' => $group->id]);

		$user = User::factory()->create();
		$user->groups()->sync([$group->id]);
		$user->refresh();

		$this->actingAs($user);

		$request = Request::create('/admin', 'GET');

		$middleware = new AdminMiddleware;

		$response = $middleware->handle($request, function () {
		});

		$this->assertEquals($response, null);
	}

	/** @test */
	public function admins_are_not_redirected()
	{
		$group = UserGroup::factory()->create();

		config(['litlife.admin_group_id' => $group->id]);

		$user = User::factory()->create();

		$this->actingAs($user);

		$request = Request::create('/admin', 'GET');

		$middleware = new AdminMiddleware;

		$this->expectException(HttpException::class);

		$response = $middleware->handle($request, function () {
		});
	}
}