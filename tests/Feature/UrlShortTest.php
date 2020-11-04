<?php

namespace Tests\Feature;

use App\UrlShort;
use Tests\TestCase;

class UrlShortTest extends TestCase
{
	public function testCreateNew()
	{
		$url = 'http://dev.litlife.club/' . uniqid();

		$urlShortener = UrlShort::init($url);
		$urlShortener->refresh();

		$this->assertEquals($url, $urlShortener->getFullUrl());
		$this->assertEquals(route('url.shortener', ['key' => $urlShortener->key]), $urlShortener->getShortUrl());
	}

	public function testFirstOrCreate()
	{
		$url = 'http://dev.litlife.club/' . uniqid();

		$urlShortener = UrlShort::init($url);

		$this->assertEquals($url, $urlShortener->getFullUrl());
		$this->assertEquals(route('url.shortener', ['key' => $urlShortener->key]), $urlShortener->getShortUrl());

		$urlShortener2 = UrlShort::init($url);

		$this->assertEquals($url, $urlShortener2->getFullUrl());
		$this->assertEquals(route('url.shortener', ['key' => $urlShortener2->key]), $urlShortener2->getShortUrl());
		$this->assertEquals($urlShortener->id, $urlShortener2->id);
	}

	public function testAnotherUrl()
	{
		$url = 'http://dev.litlife.club/' . uniqid();

		$urlShortener = UrlShort::init($url);

		$this->assertEquals($url, $urlShortener->getFullUrl());
		$this->assertEquals(route('url.shortener', ['key' => $urlShortener->key]), $urlShortener->getShortUrl());

		$url2 = 'http://dev.litlife.club/' . uniqid();

		$urlShortener2 = UrlShort::init($url2);

		$this->assertEquals($url2, $urlShortener2->getFullUrl());
		$this->assertEquals(route('url.shortener', ['key' => $urlShortener2->key]), $urlShortener2->getShortUrl());
		$this->assertNotEquals($urlShortener->id, $urlShortener2->id);
		$this->assertNotEquals($urlShortener->getShortUrl(), $urlShortener2->getShortUrl());
	}

	public function testEncode()
	{
		$urlShortener = new UrlShort;

		$this->assertEquals('W1', $urlShortener->encode(100));
		$this->assertEquals('jH3', $urlShortener->encode(10000));
		$this->assertEquals('WdbGf', $urlShortener->encode(100000000));
		$this->assertEquals('m5Sd6', $urlShortener->encode(45678951));
		$this->assertEquals('2vybZfQ', $urlShortener->encode(855453246234));
	}

	public function testDecode()
	{
		$urlShortener = new UrlShort;

		$this->assertEquals(45678951, $urlShortener->decode('m5Sd6'));
		$this->assertEquals(855453246234, $urlShortener->decode('2vybZfQ'));
	}

	public function testRedirect()
	{
		$url = 'http://dev.litlife.club/' . uniqid();

		$urlShortener = UrlShort::init($url);

		$this->get(route('url.shortener', ['key' => $urlShortener->key]))
			->assertRedirect($urlShortener->getFullUrl());
	}

	public function testNotFound()
	{
		$this->get(route('url.shortener', ['key' => uniqid()]))
			->assertNotFound();
	}
}
