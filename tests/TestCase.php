<?php

namespace Tests;

use Anhskohbo\NoCaptcha\Facades\NoCaptcha;
use App\Http\Middleware\EncryptCookies;
use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Illuminate\Console\Application as Artisan;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Imagick;
use ImagickPixel;
use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use Mockery;
use Mockery\Exception\InvalidCountException;
use ReflectionObject;

abstract class TestCase extends BaseTestCase
{
	use DatabaseTransactions;
	use WithFaker;

	public $images = [];

	/**
	 * Creates the application.
	 *
	 * @return Application
	 */
	public function createApplication()
	{
		$app = require __DIR__ . '/../bootstrap/app.php';

		ini_set('memory_limit', '2G');

		echo(' ' . get_called_class() . '::' . $this->getName() . ' ');

		$app->make(Kernel::class)->bootstrap();

		Hash::setRounds(4);

		return $app;
	}

	public function setUpFaker(): void
	{
		$this->faker = $this->makeFaker(config('app.faker_locale') ?? 'en_US');
	}

	public function clearTestingDirecrory()
	{
		$root = storage_path('framework/testing/disks/');

		$adapter = new Local($root);
		$filesystem = new Filesystem($adapter);

		$files = $filesystem->listContents('/', false);

		foreach ($files as $file) {
			if ($file['type'] == 'dir') {
				$filesystem->deleteDir($file['filename']);
			} else {
				$filesystem->delete($file['filename']);
			}
		}

		$this->assertEmpty($filesystem->listContents('/', false));
	}

	public function preventCaptchaValidation()
	{
		// prevent validation error on captcha
		NoCaptcha::shouldReceive('verifyResponse')
			->once()
			->andReturn(true);
	}

	public function assertSessionHasErrors($error, $bag = 'default')
	{
		$errors = pos(session('errors')->getBag($bag)->toArray());
		$this->assertContains($error, $errors);
	}

	public function fakeImageStream($width = 100, $height = 100, $extension = 'jpeg')
	{
		$tmp = tmpfile();

		$imagick = new Imagick();
		$imagick->newImage($width, $height, new ImagickPixel('white'));
		$imagick->addNoiseImage(Imagick::NOISE_RANDOM, Imagick::CHANNEL_DEFAULT);
		$imagick->setImageFormat($extension);

		fwrite($tmp, $imagick->getImageBlob());

		$key = uniqid();

		$this->images[$key] = $tmp;

		return stream_get_meta_data($this->images[$key])['uri'];
	}

	protected function disableCookiesEncryption($cookies)
	{
		$this->app->resolving(EncryptCookies::class,
			function ($object) use ($cookies) {
				$object->disableFor($cookies);
			});

		return $this;
	}

	/**
	 * Setup the test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		echo('setUp: ' . round(memory_get_usage() / 1000000, 2) . ' MB | ');

		if (!$this->app) {
			$this->refreshApplication();
		}

		$this->setUpTraits();

		foreach ($this->afterApplicationCreatedCallbacks as $callback) {
			call_user_func($callback);
		}

		Facade::clearResolvedInstances();

		Model::setEventDispatcher($this->app['events']);

		$this->setUpHasRun = true;
	}

	/**
	 * Clean up the testing environment before the next test.
	 *
	 * @return void
	 */
	protected function tearDown(): void
	{
		if ($this->app) {
			$this->callBeforeApplicationDestroyedCallbacks();

			$this->app->flush();

			$this->app = null;
		}

		$this->setUpHasRun = false;

		if (property_exists($this, 'serverVariables')) {
			$this->serverVariables = [];
		}

		if (property_exists($this, 'defaultHeaders')) {
			$this->defaultHeaders = [];
		}

		if (class_exists('Mockery')) {
			if ($container = Mockery::getContainer()) {
				$this->addToAssertionCount($container->mockery_getExpectationCount());
			}

			try {
				Mockery::close();
			} catch (InvalidCountException $e) {
				if (!Str::contains($e->getMethodName(), ['doWrite', 'askQuestion'])) {
					throw $e;
				}
			}
		}

		if (class_exists(Carbon::class)) {
			Carbon::setTestNow();
		}

		if (class_exists(CarbonImmutable::class)) {
			CarbonImmutable::setTestNow();
		}

		$this->afterApplicationCreatedCallbacks = [];
		$this->beforeApplicationDestroyedCallbacks = [];

		Artisan::forgetBootstrappers();

		if ($this->callbackException) {
			throw $this->callbackException;
		}


		$refl = new ReflectionObject($this);
		foreach ($refl->getProperties() as $prop) {
			if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
				$prop->setAccessible(true);
				$prop->setValue($this, null);
			}
		}

		echo(' tearDown ' . round(memory_get_usage() / 1000000, 2) . ' MB ');
		echo("\n");
	}

	protected function ajax()
	{
		return $this->withHeader('HTTP_X-Requested-With', 'XMLHttpRequest');
	}

	protected function acceptJson()
	{
		return $this->withHeader('Accept', 'application/json');
	}
}
