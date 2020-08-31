<?php

namespace Litlife\Url\Tests;

use Litlife\Url\Exceptions\InvalidArgument;
use Litlife\Url\Url;
use PHPUnit\Framework\TestCase;

class UrlTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testWithBasename()
	{
		$this->assertEquals('new_filename.ext', (string)Url::fromString('old_filename.extension')
			->withBasename('new_filename.ext'));

		$this->assertTrue(is_object(Url::fromString('old_filename.extension')
			->withBasename('new_filename.ext')));
	}

	public function testWithFilename()
	{
		$this->assertEquals('new_filename.extension', (string)Url::fromString('old_filename.extension')
			->withFilename('new_filename'));

		$this->assertEquals('images/new_name', (string)Url::fromString('images/old_name')
			->withFilename('new_name'));

		$this->assertEquals('images/new_name.extension', (string)Url::fromString('images/old_name.extension')
			->withFilename('new_name'));

		$this->assertEquals('images/new_name.extension?test=', (string)Url::fromString('images/old_name.extension?test=')
			->withFilename('new_name'));

		$this->assertTrue(is_object(Url::fromString('images/old_name.extension?test=')
			->withFilename('new_name')));
	}

	public function testAppendToFilename()
	{
		$this->assertEquals('images/old_name_append.extension?test=', (string)Url::fromString('images/old_name.extension?test=')
			->appendToFilename('_append'));

		$this->assertEquals('filename_append.extension', (string)Url::fromString('filename.extension')
			->appendToFilename('_append'));

		$this->assertTrue(is_object(Url::fromString('filename.extension')
			->appendToFilename('_append')));
	}

	public function testGetFileName()
	{
		$s = Url::fromString('Images/cover123.jpg?test')
			->getFilename();
		$this->assertEquals('cover123', (string)$s);

		$this->assertTrue(is_string(Url::fromString('Images/cover123.jpg?test')->getFilename()));
	}

	public function testGetExtension()
	{
		$this->assertEquals('jpg', (string)Url::fromString('Images/cover.jpg')
			->getExtension());

		$this->assertEquals('jpg', (string)Url::fromString('Images/cover.jpg ')
			->getExtension());

		$this->assertTrue(is_string(Url::fromString('Images/cover.jpg ')
			->getExtension()));
	}

	public function testWithExtension()
	{
		$this->assertEquals('Images/cover.bmp', (string)Url::fromString(' Images/cover.jpg ')
			->withExtension('bmp '));

		$this->assertTrue(is_object(Url::fromString(' Images/cover.jpg')->withExtension('bmp ')));
	}

	public function testGetBasename()
	{
		$s = Url::fromString('Images/cover.jpg?test')
			->getBasename();
		$this->assertEquals('cover.jpg', (string)$s);

		$this->assertTrue(is_string(Url::fromString('Images/cover.jpg?test')
			->getBasename()));
	}

	public function testGetQuery()
	{
		$s = Url::fromString('Images/cover123.jpg?test=123')
			->getQuery();
		$this->assertEquals('test=123', (string)$s);

		$this->assertTrue(is_string(Url::fromString('Images/cover123.jpg?test=123')
			->getQuery()));

		$s = Url::fromString('/test/test?test=test&page=1&#item')
			->getQuery();
		$this->assertEquals('test=test&page=1', (string)$s);

		//$this->assertEquals('/test/test?test=test&page=1#item', (string)$url);

		/*
		$url = Url::fromString('//?test=test#test')
			->getQuery();

		$this->assertEquals('test=test', (string)$url);
		*/
	}

	public function testGetSegments()
	{
		$ar = Url::fromString('sdfgsdfg/Images/cover123.jpg?test=123')
			->getSegments();
		$this->assertEquals(['sdfgsdfg', 'Images', 'cover123.jpg'], $ar);
	}

	public function testGetPath()
	{
		$this->assertEquals('/test/test/cover123.jpg', Url::fromString('/test/test/cover123.jpg?test=123')->getPath());
		$this->assertEquals('/opensource', Url::fromString('/opensource')->getPath());

		$url = Url::fromString('http://domain.com//#')
			->getPath();

		$this->assertEquals('/', (string)$url);

		$url = Url::fromString('http://domain.com//?key=value#')
			->getPath();

		$this->assertEquals('/', (string)$url);

		$url = Url::fromString('/?key=value#')
			->getPath();

		$this->assertEquals('/', (string)$url);

		$this->assertTrue(is_string(Url::fromString('/opensource')->getPath()));
	}

	public function testGetDirname()
	{
		$s = Url::fromString('cover123.jpg?test=123')
			->getDirname();
		$this->assertEquals('', $s);

		$s = Url::fromString('./Images/folder/cover.jpg')
			->getDirname();
		$this->assertEquals('./Images/folder', $s);

		$url = Url::fromString('https://spatie.be/opensource/laravel');
		$this->assertEquals('/opensource', $url->getDirname());

		$url = Url::fromString('https://spatie.be/opensource');
		$this->assertEquals('/', $url->getDirname());

		$this->assertTrue(is_string(Url::fromString('cover123.jpg?test=123')
			->getDirname()));

		$url = Url::fromString('file.jpg')
			->getDirname();
		$this->assertEquals('', $url);

		$url = Url::fromString('/file.jpg')
			->getDirname();
		$this->assertEquals('/', $url);
	}

	public function testWithDirname()
	{
		$url = Url::fromString('Images/cover.jpg')
			->withDirname('/');
		$this->assertEquals('/cover.jpg', (string)$url);

		$s = Url::fromString('Images/cover.jpg')
			->withDirname('/test');
		$this->assertEquals('/test/cover.jpg', (string)$s);

		$s = Url::fromString('Images/cover.jpg')
			->withDirname('test');
		$this->assertEquals('test/cover.jpg', (string)$s);

		$s = Url::fromString('Images/cover.jpg')
			->withDirname('');
		$this->assertEquals('cover.jpg', (string)$s);

		$this->assertTrue(is_object(Url::fromString('Images/cover.jpg')
			->withDirname('/test')));
	}

	public function testWithPath()
	{
		$url = Url::fromString('cover.jpg?test=test')
			->withPath('/');
		$this->assertEquals('/?test=test', (string)$url);

		$this->assertEquals('https://example.com/',
			(string)Url::fromString('https://example.com/Images/cover.jpg')->withPath('/'));

		$this->assertEquals('//example.com/',
			(string)Url::fromString('//example.com/Images/cover.jpg')->withPath('/'));

		$this->assertTrue(is_object(Url::fromString('//example.com/Images/cover.jpg')->withPath('/')));

		$this->assertEquals('https://example.com/test/test.txt',
			(string)Url::fromString('https://example.com/Images/cover.jpg')->withPath('/test/test.txt'));

		$this->assertEquals('https://example.com/test/test.txt?test=test#test',
			(string)Url::fromString('https://example.com/Images/cover.jpg?test=test#test')->withPath('/test/test.txt'));

		$this->assertEquals('https://example.com/test/?test=test',
			(string)Url::fromString('https://example.com/Images/?test=test')->withPath('/test/'));

	}

	public function testGetUserInfo()
	{
		$url = Url::fromString('https://sebastian:supersecret@spatie.be');
		$this->assertEquals('sebastian:supersecret', $url->getUserInfo());
	}

	public function testOther()
	{
		$url = Url::fromString('http://u5a7cbca264s#');

		$this->assertEquals('http://u5a7cbca264s', (string)$url);
	}

	public function testGetPathRelativelyToAnotherUrl()
	{
		$url = Url::fromString('Images/cover.jpg')
			->getPathRelativelyToAnotherUrl('OEBPS/content.opf');
		$this->assertEquals('OEBPS/Images/cover.jpg', (string)$url);

		$url = Url::fromString('cover.jpg')
			->getPathRelativelyToAnotherUrl('OEBPS/content.opf');
		$this->assertEquals('OEBPS/cover.jpg', (string)$url);

		$url = Url::fromString('../Styles/style.css')
			->getPathRelativelyToAnotherUrl('OEBPS/Text/ch1-4.xhtml');
		$this->assertEquals('OEBPS/Styles/style.css', (string)$url);

		$url = Url::fromString('../style.css')
			->getPathRelativelyToAnotherUrl('OEBPS/Text/ch1-4.xhtml');
		$this->assertEquals('OEBPS/style.css', (string)$url);

		$url = Url::fromString('../../style.css')
			->getPathRelativelyToAnotherUrl('OEBPS/Text/ch1-4.xhtml');
		$this->assertEquals('style.css', (string)$url);

		$url = Url::fromString('../Styles/folder/style.css')
			->getPathRelativelyToAnotherUrl('OEBPS/Text/ch1-4.xhtml');
		$this->assertEquals('OEBPS/Styles/folder/style.css', (string)$url);

		$url = Url::fromString('./cover.jpg')
			->getPathRelativelyToAnotherUrl('OEBPS/content.opf');
		$this->assertEquals('OEBPS/cover.jpg', (string)$url);

		$url = Url::fromString('Images/folder/cover.jpg')
			->getPathRelativelyToAnotherUrl('OEBPS/content.opf');
		$this->assertEquals('OEBPS/Images/folder/cover.jpg', (string)$url);

		$url = Url::fromString('./Images/folder/cover.jpg')
			->getPathRelativelyToAnotherUrl('OEBPS/content.opf');
		$this->assertEquals('OEBPS/Images/folder/cover.jpg', (string)$url);

		$url = Url::fromString('images/file.jpg')
			->getPathRelativelyToAnotherUrl('OEBPS/section_1.xhtml');
		$this->assertEquals('OEBPS/images/file.jpg', (string)$url);

		$url = Url::fromString('folder1/folder2/folder3/folder4/cover.jpg')
			->getPathRelativelyToAnotherUrl('OEBPS/content.opf');
		$this->assertEquals('OEBPS/folder1/folder2/folder3/folder4/cover.jpg', (string)$url);

		$url = Url::fromString('../../folder4/style.css')
			->getPathRelativelyToAnotherUrl('/folder1/folder2/folder3/section.xhtml');
		$this->assertEquals('/folder1/folder4/style.css', (string)$url);

		$url = Url::fromString('../../../folder4/style.css')
			->getPathRelativelyToAnotherUrl('/folder1/folder2/folder3/folder4/section.xhtml');
		$this->assertEquals('/folder1/folder4/style.css', (string)$url);

		$url = Url::fromString('../../file.css')
			->getPathRelativelyToAnotherUrl('/folder1/folder2/file.txt');
		$this->assertEquals('/file.css', (string)$url);

		$url = Url::fromString('../folder7/style.css')
			->getPathRelativelyToAnotherUrl('/folder2/folder3/folder4/folder5/folder6/file.txt');

		$this->assertEquals('/folder2/folder3/folder4/folder5/folder7/style.css', (string)$url);

		$url = Url::fromString('../style.css')
			->getPathRelativelyToAnotherUrl('/folder2/folder3/folder4/folder5/folder6/file.txt');

		$this->assertEquals('/folder2/folder3/folder4/folder5/style.css', (string)$url);

		$url = Url::fromString('folder2/file.jpg')
			->getPathRelativelyToAnotherUrl('folder1/file.txt');
		$this->assertEquals('folder1/folder2/file.jpg', (string)$url);

		$url = Url::fromString('folder2/file.jpg')
			->getPathRelativelyToAnotherUrl('/folder1/file.txt');
		$this->assertEquals('/folder1/folder2/file.jpg', (string)$url);

		$url = Url::fromString('../../file.css')
			->getPathRelativelyToAnotherUrl('/folder1/folder2/file.txt');
		$this->assertEquals('/file.css', (string)$url);

		$url = Url::fromString('../../style.css')
			->getPathRelativelyToAnotherUrl('OEBPS/Text/ch1-4.xhtml');
		$this->assertEquals('style.css', (string)$url);
	}

	public function testGetPathQueryFragment()
	{
		$url = Url::fromString('http://domain.com/dirname/file.txt?key=value#fragment')
			->getPathQueryFragment();

		$this->assertEquals('/dirname/file.txt?key=value#fragment', (string)$url);

		$url = Url::fromString('http://domain.com/dirname/file.txt?')
			->getPathQueryFragment();

		$this->assertEquals('/dirname/file.txt', (string)$url);

		$url = Url::fromString('http://domain.com/dirname/file.txt?key=value#')
			->getPathQueryFragment();

		$this->assertEquals('/dirname/file.txt?key=value', (string)$url);

		$url = Url::fromString('http://domain.com/dirname/?key=value#')
			->getPathQueryFragment();

		$this->assertEquals('/dirname/?key=value', (string)$url);

		$url = Url::fromString('http://domain.com//?key=value#')
			->getPathQueryFragment();

		$this->assertEquals('/?key=value', (string)$url);

		$url = Url::fromString('test?key=value#')
			->getPathQueryFragment();

		$this->assertEquals('test?key=value', (string)$url);

		$url = Url::fromString('http://domain.com/test/?key=value#fragment')
			->getPathQueryFragment();

		$this->assertEquals('/test/?key=value#fragment', (string)$url);

		$url = Url::fromString('http://domain.com/test?key=value#fragment')
			->getPathQueryFragment();

		$this->assertEquals('/test?key=value#fragment', (string)$url);

		$url = Url::fromString('http://domain.com/?key=value#fragment')
			->getPathQueryFragment();

		$this->assertEquals('/?key=value#fragment', (string)$url);

		$url = Url::fromString('?test=test')
			->getPathQueryFragment();

		$this->assertEquals('/?test=test', (string)$url);

		$url = Url::fromString('/test/test?test=test&page=1&#item')
			->getPathQueryFragment();

		$this->assertEquals('/test/test?test=test&page=1#item', (string)$url);
		$this->assertEquals('/test/test', $url->getPath());
		$this->assertEquals('test=test&page=1', $url->getQuery());
	}

	public function testGetDirnameArray()
	{
		$array = Url::fromString('file.txt')
			->getDirnameArray();

		$this->assertEquals(0, count($array));
	}

	public function testGetDirnameArrayWithoutEmpty()
	{
		$array = Url::fromString('/folder1/folder2/file.txt')
			->getDirnameArrayWithoutEmpty();

		$this->assertEquals('folder1', $array[0]);
		$this->assertEquals('folder2', $array[1]);
		$this->assertCount(2, $array);

		$array = Url::fromString('/folder1/file.txt')
			->getDirnameArrayWithoutEmpty();

		$this->assertEquals('folder1', $array[0]);
		$this->assertCount(1, $array);

		$array = Url::fromString('/file.txt')
			->getDirnameArrayWithoutEmpty();

		$this->assertFalse(isset($array[0]));
		$this->assertCount(0, $array);
	}

	public function testInvalidUrl()
	{
		$url = 'http://#u<a>5a7cbca264s#';

		$this->expectException(InvalidArgument::class);
		$this->expectExceptionMessage(InvalidArgument::failedParseUrl($url)->getMessage());

		$url = Url::fromString($url);
		$this->assertEquals('', (string)$url);
	}

	public function testInvalidUrl2()
	{
		// TODO

		$url = 'http://example.com/<script>alert(1);</script>';

		$this->assertEquals($url, (string)Url::fromString($url));
	}

	public function testWithQueryParameter()
	{
		$url = Url::fromString('/test')
			->withQueryParameter('key', 'value');

		$this->assertEquals('/test?key=value', (string)$url);

		$value = 'http://example.com/test/?key=value&foo=bar#fragment';

		$url = Url::fromString('/test')
			->withQueryParameter('key', $value);

		$this->assertEquals('/test?key=http%3A%2F%2Fexample.com%2Ftest%2F%3Fkey%3Dvalue%26foo%3Dbar%23fragment', (string)$url);
		$this->assertEquals($value, $url->getQueryParameter('key'));

		$url = Url::fromString('/test')
			->withQueryParameter('key', 'value value');

		$this->assertEquals('/test?key=value+value', (string)$url);

		$url = Url::fromString('/test')
			->withQueryParameter('key', 'тест тест');

		$this->assertEquals('/test?key=%D1%82%D0%B5%D1%81%D1%82+%D1%82%D0%B5%D1%81%D1%82', (string)$url);

		$url = (new Url())
			->withDirname('/test')
			->withQueryParameter('key', 'тест тест');

		$this->assertEquals('/test?key=%D1%82%D0%B5%D1%81%D1%82+%D1%82%D0%B5%D1%81%D1%82', (string)$url);
	}

	public function with_path_returns_a_new_instance()
	{
		$url = Url::fromString('https://spatie.be');
		$clone = $url->withPath('/opensource');
		$this->assertEquals('/', $url->getPath());
		$this->assertEquals('/opensource', $clone->getPath());
	}

	/** @test */
	public function with_query_returns_a_new_instance()
	{
		$url = Url::fromString('https://spatie.be');
		$clone = $url->withQuery('utm_source=phpunit');
		$this->assertEquals('', $url->getQuery());
		$this->assertEquals('utm_source=phpunit', $clone->getQuery());
	}

	/** @test */
	public function it_can_check_if_it_matches_another_url()
	{
		$url = Url::fromString('https://spatie.be/');
		$this->assertTrue($url->matches(Url::fromString('https://spatie.be/')));
	}

	/** @test */
	public function it_can_check_if_it_doesnt_match_another_url()
	{
		$url = Url::fromString('https://spatie.be');
		$this->assertFalse($url->matches(Url::fromString('https://spatie.be/opensource')));
	}

	/** @test */
	public function it_can_check_if_it_contains_a_mailto()
	{
		$url = Url::fromString('mailto:email@domain.tld');
		$this->assertTrue($url->matches(Url::fromString('mailto:email@domain.tld')));
	}

	/** @test */
	public function it_can_get_a_query_parameter()
	{
		$url = Url::create()->withQuery('offset=10');
		$this->assertEquals(10, $url->getQueryParameter('offset'));
	}

	/** @test */
	public function it_returns_null_if_a_query_parameter_doesnt_exist()
	{
		$url = Url::create()->withQuery('offset=10');
		$this->assertNull($url->getQueryParameter('limit'));
	}

	/** @test */
	public function it_can_return_a_default_if_a_query_parameter_doesnt_exist()
	{
		$url = Url::create()->withQuery('offset=10');
		$this->assertEquals(20, $url->getQueryParameter('limit', 20));
	}

	/** @test */
	public function it_can_return_all_parameters()
	{
		$url = Url::create()->withQuery('offset=10');
		$this->assertEquals(['offset' => 10], $url->getAllQueryParameters());
	}

	/** @test */
	public function it_can_set_a_query_parameter()
	{
		$url = Url::create()->withQueryParameter('offset', 10);
		$this->assertEquals(10, $url->getQueryParameter('offset'));
	}

	/** @test */
	public function it_can_check_if_it_has_a_query_parameter()
	{
		$url = Url::create()->withQuery('offset=10');
		$this->assertTrue($url->hasQueryParameter('offset'));
		$this->assertFalse($url->hasQueryParameter('limit'));
	}

	/** @test */
	public function it_can_unset_a_query_parameter()
	{
		$url = Url::create()
			->withQuery('offset=10')
			->withoutQueryParameter('offset');
		$this->assertFalse($url->hasQueryParameter('offset'));
	}

	/** @test */
	public function it_can_handle_empty_query_parameters()
	{
		$url = Url::create()->withQuery('offset');
		$this->assertTrue($url->hasQueryParameter('offset'));
	}

	/** @test */
	public function empty_query_parameters_default_to_null()
	{
		$url = Url::create()->withQuery('offset');
		$this->assertEquals('', $url->getQueryParameter('offset'));
	}

	public function testRelativePathUrl()
	{

		$url = Url::fromString('/folder1/folder2/other.txt');
		$this->assertEquals('folder2/other.txt', (string)$url->getRelativePathUrl('/folder1/file.txt'));

		$url = Url::fromString('/folder1/folder2/folder3/other.txt');
		$this->assertEquals('folder2/folder3/other.txt', (string)$url->getRelativePathUrl('/folder1/file.txt'));

		$url = Url::fromString('/folder1/folder2/folder3/folder4/other.txt');
		$this->assertEquals('folder2/folder3/folder4/other.txt', (string)$url->getRelativePathUrl('/folder1/file.txt'));

		$url = Url::fromString('/folder1/folder2/folder3/folder4/other.txt');
		$this->assertEquals('folder3/folder4/other.txt', (string)$url->getRelativePathUrl('/folder1/folder2/file.txt'));

		$url = Url::fromString('/other.txt');
		$this->assertEquals('../other.txt', (string)$url->getRelativePathUrl('/dirname/file.txt'));

		$url = Url::fromString('/folder1/other.txt');
		$this->assertEquals('../folder1/other.txt', (string)$url->getRelativePathUrl('/folder2/file.txt'));

		$url = Url::fromString('/folder1/folder2/other.txt');
		$this->assertEquals('../../folder1/folder2/other.txt', (string)$url->getRelativePathUrl('/folder3/folder4/file.txt'));

		$url = Url::fromString('/folder1/folder2/folder3/other.txt');
		$this->assertEquals('folder3/other.txt', (string)$url->getRelativePathUrl('/folder1/folder2/file.txt'));
	}

	public function testWithDirnameArray()
	{
		$url = Url::fromString('/folder1/file.txt');

		$this->assertEquals('/folder2/file.txt', (string)$url->withDirnameArray(['', 'folder2']));

		$this->assertEquals('folder2/file.txt', (string)$url->withDirnameArray(['folder2']));

		$this->assertEquals('/file.txt', (string)$url->withDirnameArray(['']));
	}

	public function testUrlencode()
	{
		$url = 'http://dev.litlife.club/папка/файл.txt?ключ=значение';

		$url = Url::fromString($url)->urlencode();

		$this->assertEquals('http://dev.litlife.club/%D0%BF%D0%B0%D0%BF%D0%BA%D0%B0/%D1%84%D0%B0%D0%B9%D0%BB.txt?%D0%BA%D0%BB%D1%8E%D1%87=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5',
			(string)$url);
		$this->assertEquals('/%D0%BF%D0%B0%D0%BF%D0%BA%D0%B0/%D1%84%D0%B0%D0%B9%D0%BB.txt', $url->getPath());
		$this->assertEquals('%D0%BA%D0%BB%D1%8E%D1%87=%D0%B7%D0%BD%D0%B0%D1%87%D0%B5%D0%BD%D0%B8%D0%B5', $url->getQuery());
	}

	public function testFragment()
	{
		$s = '/away?url=http%3A%2F%2Fexample.com%2Ftest%23fragment';
		$url = Url::fromString($s);
		$this->assertEquals('', $url->getFragment());

		$s = '/away?url=http%3A%2F%2Fexample.com%2Ftest%2323fragment#fragment2';
		$url = Url::fromString($s);
		$this->assertEquals('fragment2', $url->getFragment());
	}

	public function test()
	{
		$fullPath = (string)Url::fromString('cover.jpeg')
			->getPathRelativelyToAnotherUrl('content.opf')
			->withoutFragment();

		$this->assertEquals('cover.jpeg', $fullPath);
	}

	public function testSlashAfterHostMustExistsIfQueryStrExists()
	{
		$url = (new Url())
			->withQueryParameter('key', 'value')
			->withHost('example.com')
			->withScheme('https');

		$this->assertEquals('https://example.com/?key=value', (string)$url);
	}

	public function testSlashAfterHostMustExistsIfHashStrExists()
	{
		$url = (new Url())
			->withFragment('key')
			->withHost('example.com')
			->withScheme('https');

		$this->assertEquals('https://example.com/#key', (string)$url);
	}

	public function testDontAddSlashIfHostEmpty()
	{
		$url = (new Url())
			->withFragment('key');

		$this->assertEquals('key', $url->getFragment());
		$this->assertEquals('#key', (string)$url);
	}

	public function testSlashExistsIfQueryNotEmptyAndHostEmpty()
	{
		$url = (new Url())
			->withQueryParameter('key', 'value');

		$this->assertEquals('/?key=value', (string)$url);
	}
}
