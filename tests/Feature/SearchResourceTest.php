<?php

namespace Tests\Feature;

use App\Library\SearchResource;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Tests\TestCase;

class SearchResourceTest extends TestCase
{
	public function testSetGetDefaultInputValue()
	{
		$request = new Request();
		$builder = Builder::class;

		$resource = new SearchResource($request, $builder);

		$resource->setDefaultInputValue('key', 'value');

		$this->assertEquals('value', $resource->getDefaultInputValue('key'));
	}

	public function testGetInputValueIfOnlyDefaultExists()
	{
		$request = new Request();
		$builder = Builder::class;

		$resource = new SearchResource($request, $builder);

		$resource->setDefaultInputValue('key', 'value');

		$this->assertEquals('value', $resource->getInputValue('key'));
	}

	public function testGetInputValueIfExists()
	{
		$request = new Request();
		$builder = Builder::class;

		$resource = new SearchResource($request, $builder);

		$resource->setDefaultInputValue('key', 'value');
		$resource->setInputValue('key', 'value2');

		$this->assertEquals('value2', $resource->getInputValue('key'));
	}
}
