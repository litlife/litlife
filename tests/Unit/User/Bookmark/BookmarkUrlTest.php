<?php

namespace Tests\Unit\User\Bookmark;

use App\Bookmark;
use PHPUnit\Framework\TestCase;

class BookmarkUrlTest extends TestCase
{
	public function test1()
	{
		$bookmark = new Bookmark;
		$bookmark->url = '?test=test';
		$this->assertEquals('/?test=test', $bookmark->url);
	}

	public function test2()
	{
		$bookmark = new Bookmark;
		$bookmark->url = 'https://www.test.com:8080/test?test=test&key=value#test';
		$this->assertEquals('/test?test=test&key=value', $bookmark->url);
	}

	public function test3()
	{
		$bookmark = new Bookmark;
		$bookmark->url = 'https://www.test.com/test?test=test&key=value#test';
		$this->assertEquals('/test?test=test&key=value', $bookmark->url);
	}

	public function test4()
	{
		$bookmark = new Bookmark;
		$bookmark->url = '/test?test=test&key=value#test';
		$this->assertEquals('/test?test=test&key=value', $bookmark->url);
	}

	public function test5()
	{
		$bookmark = new Bookmark;
		$bookmark->url = 'test?test=test&key=value#test';
		$this->assertEquals('/test?test=test&key=value', $bookmark->url);
	}

	public function test6()
	{
		$bookmark = new Bookmark;
		$bookmark->url = '/test?test=test&key=value#';
		$this->assertEquals('/test?test=test&key=value', $bookmark->url);
	}

	public function test7()
	{
		$bookmark = new Bookmark;
		$bookmark->url = '/test?test=test';
		$this->assertEquals('/test?test=test', $bookmark->url);
	}

	public function test8()
	{
		$bookmark = new Bookmark;
		$bookmark->url = '/test?';
		$this->assertEquals('/test', $bookmark->url);
	}

	public function test9()
	{
		$bookmark = new Bookmark;
		$bookmark->url = 'test?';
		$this->assertEquals('/test', $bookmark->url);
	}

	public function test10()
	{
		$bookmark = new Bookmark;
		$bookmark->url = '/?test=test';
		$this->assertEquals('/?test=test', $bookmark->url);
	}

	public function test11()
	{
		$bookmark = new Bookmark;
		$bookmark->url = '?';
		$this->assertEquals('/', $bookmark->url);
	}

	public function test12()
	{
		$bookmark = new Bookmark;
		$bookmark->url = '/test/?test=test';
		$this->assertEquals('/test/?test=test', $bookmark->url);
	}

	public function test13()
	{
		$bookmark = new Bookmark;
		$bookmark->url = '/test/test?test=test&page=1&#item';
		$this->assertEquals('/test/test?test=test&page=1', $bookmark->url);
	}

	public function test14()
	{
		$bookmark = new Bookmark;
		$bookmark->url = '/books?genre%5B%5D=130&genre%5B%5D=131&order=rating_avg_down&view=gallery';
		$this->assertEquals('/books?genre%5B%5D=130&genre%5B%5D=131&order=rating_avg_down&view=gallery', $bookmark->url);
	}

	public function test15()
	{
		$bookmark = new Bookmark;
		$bookmark->url = 'https://www.test.com:8080/test?test=test&key=value#test';
		$this->assertEquals('/test?test=test&key=value', $bookmark->url);
	}
}
