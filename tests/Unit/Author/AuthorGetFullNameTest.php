<?php

namespace Tests\Unit\Author;

use App\Author;
use PHPUnit\Framework\TestCase;

class AuthorGetFullNameTest extends TestCase
{
    public function test()
    {
        $author = new Author();
        $author->last_name = 'Фамилия';
        $author->first_name = 'Имя';
        $author->middle_name = 'Отчество';
        $author->nickname = 'Ник';

        $this->assertEquals('Фамилия Имя Отчество Ник', $author->full_name);
    }

    public function testTrim()
    {
        $author = new Author();
        $author->last_name = '  Фамилия  ';
        $author->first_name = '  Имя  ';
        $author->middle_name = '  Отчество  ';
        $author->nickname = '  Ник  ';

        $this->assertEquals('Фамилия Имя Отчество Ник', $author->full_name);
    }
}
