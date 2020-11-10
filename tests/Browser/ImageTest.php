<?php

namespace Tests\Browser;

use App\User;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ImageTest extends DuskTestCase
{
	/** @test */

	public function testUploadInSceditor()
	{
		$this->browse(function (Browser $browser) {

			$user = User::factory()->create();
			$user->push();

			$browser->resize(1200, 2080);

			$browser->loginAs($user)
				->visit(route('profile', $user))
				->waitFor('.sceditor-container')
				->waitFor('.sceditor-button-uploadImage')
				->click('.sceditor-button-uploadImage')
				->whenAvailable('.bootbox', function ($bootbox) {
					$bootbox->waitFor('form', 15)
						->waitFor('[name=upload]')
						->assertVisible('[name=upload]')
						->attach('upload', __DIR__ . '/images/test.jpeg');
				})
				->waitUntilMissing('.bootbox')
				->assertMissing('.bootbox')
				->press(__('common.create'));

			$image = $user->images()->first();

			$bb_text = $user->blog()->first()->bb_text;

			$this->assertStringContainsString($image->url, $bb_text);
		});
	}

	/*
	public function testClamavValidationRuleWorks()
	{
		$_ENV['APP_RUNNING_IN_CONSOLE'] = false;

		$this->assertFalse(Env::get('APP_RUNNING_IN_CONSOLE'));
		$this->assertFalse($this->app->runningInConsole());
		$this->refreshApplication();
		$this->assertFalse($this->app->runningInConsole());

		Storage::fake(config('filesystems.default'));

		$user = User::factory()->create()
			->fresh();

		$upload = UploadedFile::fake()->image('avatar.jpg');

		$response = $this->actingAs($user)
			->post(route('images.store'), [
				'upload' => $upload
			], ['HTTP_X-Requested-With' => 'XMLHttpRequest']
			)
			->assertSessionHasNoErrors()
			->assertJsonMissingValidationErrors()
			->assertStatus(201);

		unset($_ENV['APP_RUNNING_IN_CONSOLE']);
		$this->refreshApplication();
		$this->assertTrue($this->app->runningInConsole());

		$image = $user->fresh()->images()->first();

		$this->assertNotNull($image);
	}
	*/

}
