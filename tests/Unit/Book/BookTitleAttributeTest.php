<?php

namespace Tests\Unit\Book;

use App\Book;
use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;

class BookTitleAttributeTest extends TestCase
{
    public function testDefault()
    {
        $book = new Book();

        $this->assertEquals('', $book->title);
    }

    public function testOverflow()
    {
        $title = Str::random(300);

        $book = new Book();
        $book->title = $title;

        $this->assertEquals(mb_substr($title, 0, 255), $book->title);
    }

    public function testTrim()
    {
        $book = new Book();
        $book->title = ' test';

        $this->assertEquals('test', $book->title);
    }

    public function testSi()
    {
        $book = new Book();
        $book->title = '    title   (СИ)';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_si);

        $book->refresh();
        $book->title = 'title   [СИ]';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_si);

        $book->refresh();
        $book->title = ' title (сИ)';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_si);

        $book->refresh();
        $book->title = ' title    (Си)';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_si);

        $book->refresh();
        $book->title = ' title      (Cи)';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_si);
    }

    public function testLp()
    {
        $book = new Book();
        $book->is_lp = false;

        $book->title = 'title (ЛП)';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_lp);

        $book->refresh();
        $book->title = 'title [ЛП]';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_lp);

        $book->refresh();
        $book->title = 'title (лП)';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_lp);

        $book->refresh();
        $book->title = 'title (Лп)';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_lp);
    }

    public function testCollection()
    {
        $book = new Book();
        $book->is_collection = false;

        $book->title = 'title (Сборник)';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_collection);

        $book->refresh();
        $book->title = 'title [СБОРНИК]';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_collection);

        $book->refresh();
        $book->title = 'title (СбоРНИК)';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_collection);

        $book->refresh();
        $book->title = 'title (сБорник)';

        $this->assertEquals('title', $book->title);
        $this->assertTrue($book->is_collection);
    }

    public function testManySpacesToOne()
    {
        $book = new Book();
        $book->title = 'заголовок   заголовок    заголовок';

        $this->assertEquals('заголовок заголовок заголовок', $book->title);
    }

    public function testDontStripTags()
    {
        $book = new Book();
        $book->title = '<p>заголовок</p>';

        $this->assertEquals('<p>заголовок</p>', $book->title);
    }
}
