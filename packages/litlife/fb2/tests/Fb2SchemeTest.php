<?php

namespace Litlife\Fb2\Tests;

use DOMElement;
use Litlife\Fb2\Scheme;
use PHPUnit\Framework\TestCase;

class Fb2SchemeTest extends TestCase
{
	public function testGetFictionBookRule()
	{
		$scheme = $this->scheme();
		$this->assertInstanceOf(DOMElement::class, $scheme->getFictionBookRule());
	}

	public function scheme()
	{
		$scheme = new Scheme();
		$scheme->loadScheme();
		return $scheme;
	}

	public function testGetDescriptionRule()
	{
		$scheme = $this->scheme();
		$this->assertInstanceOf(DOMElement::class, $scheme->getDescriptionRule());
	}
}
