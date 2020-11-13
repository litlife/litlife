<?php

namespace Tests\Feature\Artisan;

use App\AuthorParsedData;
use App\Console\Commands\SamLibParse;
use Tests\TestCase;

class SamLibParseTest extends TestCase
{
    public function testGetUrlsFromHtml()
    {
        $html = '<a href="/x/index_x.shtml">X</a> 
<a href="/y/index_y.shtml">Y</a>';

        $parse = new SamLibParse;

        $this->assertEquals(['http://samlib.ru/x/index_x.shtml', 'http://samlib.ru/y/index_y.shtml'],
            $parse->getUrlsFromHtml($html));
    }

    public function testGetAuthorsUrls()
    {
        $html = 'test <a href="http://samlib.ru/a/amelin_a_w/">X</a> 
dfgdf <a href="/k/kirena_s/">X</a> 
<a href="/y/index_y.shtml">Y</a>
<a href="/y/">Y</a>
<a href="/kr/kirena_s/">X</a> 
<a href="/2/27rus27/">X</a>';

        $parse = new SamLibParse;

        $this->assertEquals([
            'http://samlib.ru/a/amelin_a_w/',
            'http://samlib.ru/k/kirena_s/',
            'http://samlib.ru/2/27rus27/'
        ],
            $parse->getAuthorsUrls($html));
    }

    public function testGetAuthorData()
    {
        $html = '<html>
<head>
		<title>Журнал "Самиздат".Иванов Василий Павлович. Книги </title>
	<link rel="openid.server" href="http://samlib.ru/cgi-bin/oid_login" />
		<link rel="openid2.provider" href="http://samlib.ru/cgi-bin/oid_login" />
</head>
<body bgcolor="#e9e9e9" >
<center>
<h3>Иванов Василий Павлович:<br>
<font color="#cc5555">Книги</font></h3>

<br>
<table width=50% align=right bgcolor="#e0e0e0" cellpadding=5>
<tr><td>
<ul>
 <li><b>Aдpeс:</b> <u>&#100&#111&#110&#109&#97&#105&#108&#64&#109&#97&#105&#108&#46&#117&#97</u>
 <li><b><a href=/rating/bday/><font color=#393939>Родился:</font></a></b> 01/01/1990
 <li><b><a href=/rating/town/><font color=#393939>Живет:</font></a></b> Россия, Москва
 <li><b><a href=/long.shtml><font color=#393939>Обновлялось:</font></a></b> 01/01/2015
 <li><b><a href=/rating/size/><font color=#393939>Объем:</font></a></b> 100k/23
 <li><b><a href=/rating/author/><font color=#393939>Рейтинг:</font></a></b> 8.55*5
 <li><b><a href=stat.shtml><font color=#393939>Посетителей за год:</font></a></b> 500
 <li><b><a href=/cgi-bin/frlist?DIR=a/amelin_w_p><font color=#393939>Friends/Friend Of:</font></a></b> 10/15
</ul>
</td></tr></table>
</body>
</html>
';

        $parse = new SamLibParse;
        $data = $parse->parseAuthorData(mb_convert_encoding($html, 'windows-1251', 'utf-8'));

        $this->assertEquals('Иванов Василий Павлович', $data['name']);
        $this->assertEquals('donmail@mail.ua', $data['email']);
        $this->assertEquals('Россия, Москва', $data['city']);
        $this->assertEquals('8.55*5', $data['rating']);
    }

    public function testGetAuthorDataNotExistsH3()
    {
        $html = '<html>
<head>
		<title>Журнал "Самиздат".Иванов Василий Павлович. Книги </title>
	<link rel="openid.server" href="http://samlib.ru/cgi-bin/oid_login" />
		<link rel="openid2.provider" href="http://samlib.ru/cgi-bin/oid_login" />
</head>
<body bgcolor="#e9e9e9" >
<center>

<br>
<table width=50% align=right bgcolor="#e0e0e0" cellpadding=5>
<tr><td>
<ul>
 <li><b><a href=/rating/bday/><font color=#393939>Родился:</font></a></b> 01/01/1990
 <li><b><a href=/rating/town/><font color=#393939>Живет:</font></a></b> Россия, Москва
 <li><b><a href=/long.shtml><font color=#393939>Обновлялось:</font></a></b> 01/01/2015
 <li><b><a href=/rating/size/><font color=#393939>Объем:</font></a></b> 100k/23
 <li><b><a href=/rating/author/><font color=#393939>Рейтинг:</font></a></b> 8.55*5
 <li><b><a href=stat.shtml><font color=#393939>Посетителей за год:</font></a></b> 500
 <li><b><a href=/cgi-bin/frlist?DIR=a/amelin_w_p><font color=#393939>Friends/Friend Of:</font></a></b> 10/15
</ul>
</td></tr></table>
</body>
</html>
';

        $parse = new SamLibParse;
        $data = $parse->parseAuthorData(mb_convert_encoding($html, 'windows-1251', 'utf-8'));

        $this->assertEquals('', $data['name']);
        $this->assertEquals('', $data['email']);
        $this->assertEquals('Россия, Москва', $data['city']);
        $this->assertEquals('8.55*5', $data['rating']);
    }
    /*
        public function testInsertToDB()
        {
            $url = 'http://example.com/test?sfd';

            $data = [
                'name' => 'Иванов Василий Павлович',
                'email' => $this->faker->email,
                'city' => $this->faker->city,
                'rating' => rand(1, 1000),
            ];

            $parse = new SamLibParse;
            $parse->insertOrUpdate($url, $data);

            $parsedData = AuthorParsedData::where('url', $url)->first();

            $this->assertNotNull($parsedData);

            $this->assertEquals($url, $parsedData->url);
            $this->assertEquals($data['name'], $parsedData->name);
            $this->assertEquals($data['email'], $parsedData->email);
            $this->assertEquals($data['city'], $parsedData->city);
            $this->assertEquals($data['rating'], $parsedData->rating);
        }
    */
    /*
        public function testUpdateDataToDB()
        {
            $url = 'http://example.com/test?sfd';

            $data = [
                'name' => 'Иванов Василий Павлович',
                'email' => $this->faker->email,
                'city' => $this->faker->city,
                'rating' => rand(1, 1000),
            ];

            $parse = new SamLibParse;
            $parse->insertOrUpdate($url, $data);

            $parsedData = AuthorParsedData::where('url', $url)->first();

            $this->assertNotNull($parsedData);

            $data = [
                'name' => 'Василий Иванов Павлович',
                'email' => $this->faker->email,
                'city' => $this->faker->city,
                'rating' => rand(1, 1000),
            ];

            $parse->insertOrUpdate($url, $data);

            $parsedData->refresh();

            $this->assertEquals($data['name'], $parsedData->name);
            $this->assertEquals($data['email'], $parsedData->email);
            $this->assertEquals($data['city'], $parsedData->city);
            $this->assertEquals($data['rating'], $parsedData->rating);
        }
    */
    public function testGetAuthorDataH3Empty()
    {
        $html = '<html>
<head>
		<title>/title>
</head>
<body bgcolor="#e9e9e9" >
<center>
<h3></h3>
</body>
</html>
';

        $parse = new SamLibParse;
        $data = $parse->parseAuthorData($html);

        $this->assertEquals('', $data['name']);
        $this->assertEquals('', $data['email']);
        $this->assertEquals('', $data['city']);
        $this->assertEquals('', $data['rating']);
    }

    public function testGetAuthorDataCharset()
    {
        $html = '<html>
<head>
		<title>Журнал "Самиздат".Иванов Василий Павлович. Книги </title>
	<link rel="openid.server" href="http://samlib.ru/cgi-bin/oid_login" />
		<link rel="openid2.provider" href="http://samlib.ru/cgi-bin/oid_login" />
</head>
<body bgcolor="#e9e9e9" >
<center>
<h3>Иванов Василий Павлович:<br>
<font color="#cc5555">Книги</font></h3>

<br>
<table width=50% align=right bgcolor="#e0e0e0" cellpadding=5>
<tr><td>
<ul>
 <li><b>Aдpeс:</b> <u>&#100&#111&#110&#109&#97&#105&#108&#64&#109&#97&#105&#108&#46&#117&#97</u>
 <li><b><a href=/rating/bday/><font color=#393939>Родился:</font></a></b> 01/01/1990
 <li><b><a href=/rating/town/><font color=#393939>Живет:</font></a></b> Россия, Москва
 <li><b><a href=/long.shtml><font color=#393939>Обновлялось:</font></a></b> 01/01/2015
 <li><b><a href=/rating/size/><font color=#393939>Объем:</font></a></b> 100k/23
 <li><b><a href=/rating/author/><font color=#393939>Рейтинг:</font></a></b> 8.55*5
 <li><b><a href=stat.shtml><font color=#393939>Посетителей за год:</font></a></b> 500
 <li><b><a href=/cgi-bin/frlist?DIR=a/amelin_w_p><font color=#393939>Friends/Friend Of:</font></a></b> 10/15
</ul>
</td></tr></table>
</body>
</html>
';

        $parse = new SamLibParse;
        $data = $parse->parseAuthorData(mb_convert_encoding($html, 'windows-1251', 'utf-8'));

        $this->assertEquals('Иванов Василий Павлович', $data['name']);
        $this->assertEquals('donmail@mail.ua', $data['email']);
        $this->assertEquals('Россия, Москва', $data['city']);
        $this->assertEquals('8.55*5', $data['rating']);
    }

    /*
        public function testIsPageParsed()
        {
            $url = 'http://example.com/test?sfd';

            $data = [
                'name' => 'Иванов Василий Павлович',
                'email' => $this->faker->email,
                'city' => $this->faker->city,
                'rating' => rand(1, 1000),
            ];

            $parse = new SamLibParse;

            $this->assertFalse($parse->isUrlParsed($url));

            $parse->insertOrUpdate($url, $data);

            $this->assertTrue($parse->isUrlParsed($url));
        }
        */

    public function testGetUrlsFromHtmlWithNoUrls()
    {
        $html = 'esdfsd fsd fsd';

        $parse = new SamLibParse;

        $this->assertEquals([], $parse->getUrlsFromHtml($html));
    }

    public function testGetAbsoluteRating()
    {
        $parsedData = new AuthorParsedData;

        $parsedData->rating = '3.70*13';

        $this->assertEquals('48', $parsedData->getAbsoluteRating());

        $parsedData->rating = '0';

        $this->assertEquals('0', $parsedData->getAbsoluteRating());

        $parsedData->rating = null;

        $this->assertEquals('0', $parsedData->getAbsoluteRating());
    }

    public function testIsEmailVaild()
    {
        $parsedData = new AuthorParsedData;

        $parsedData->email = '3.70*13';

        $this->assertFalse($parsedData->IsEmailVaild());

        $parsedData->email = '0';

        $this->assertFalse($parsedData->IsEmailVaild());

        $parsedData->email = null;

        $this->assertFalse($parsedData->IsEmailVaild());

        $parsedData->email = 'sdfsfd@sdfsdf.sdf';

        $this->assertTrue($parsedData->IsEmailVaild());

        $parsedData->email = 'sdfsfd@вапвап.ыва';

        $this->assertFalse($parsedData->isEmailVaild());
    }

    public function testGetEmail()
    {
        $parsedData = new AuthorParsedData;

        $parsedData->email = 'test [at] gmail [dot] com';

        $this->assertEquals('test@gmail.com', $parsedData->email);

        $parsedData->email = 'test[]gmail.com';

        $this->assertEquals('test@gmail.com', $parsedData->email);

        $parsedData->email = 'test[]gmail[.com';

        $this->assertEquals('test@gmail.com', $parsedData->email);

        $parsedData->email = 'test [sobaka] gmail [dot] com';

        $this->assertEquals('test@gmail.com', $parsedData->email);
    }
}
