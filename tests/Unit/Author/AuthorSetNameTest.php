<?php

namespace Tests\Unit\Author;

use App\Author;
use PHPUnit\Framework\TestCase;

class AuthorSetNameTest extends TestCase
{
    public function testOneWord()
    {
        $author = new Author();
        $author->name = 'Ник';

        $this->assertEquals('', $author->last_name);
        $this->assertEquals('', $author->first_name);
        $this->assertEquals('', $author->middle_name);
        $this->assertEquals('Ник', $author->nickname);
    }

    public function testTwoWords()
    {
        $author = new Author();
        $author->name = ' Фамилия  Имя';

        $this->assertEquals('Фамилия', $author->last_name);
        $this->assertEquals('Имя', $author->first_name);
        $this->assertEquals('', $author->middle_name);
        $this->assertEquals('', $author->nickname);
    }

    public function testThreeWords()
    {
        $author = new Author();
        $author->name = 'Фамилия  Имя  Отчество ';

        $this->assertEquals('Фамилия', $author->last_name);
        $this->assertEquals('Имя', $author->first_name);
        $this->assertEquals('Отчество', $author->middle_name);
        $this->assertEquals('', $author->nickname);
    }

    public function testFourWords()
    {
        $author = new Author();
        $author->name = ' Фамилия  Имя  Отчество  Ник ';

        $this->assertEquals('Фамилия', $author->last_name);
        $this->assertEquals('Имя', $author->first_name);
        $this->assertEquals('Отчество', $author->middle_name);
        $this->assertEquals('Ник', $author->nickname);
    }

    public function testName()
    {
        $author = new Author;
        $author->name = 'Lastname  Firstname  Middlename Nickname';

        $this->assertEquals('Lastname', $author->last_name);
        $this->assertEquals('Firstname', $author->first_name);
        $this->assertEquals('Middlename', $author->middle_name);
        $this->assertEquals('Nickname', $author->nickname);
        $this->assertEquals('Lastname Firstname Middlename Nickname', $author->name);

        $author = new Author;
        $author->name = 'Nickname';

        $this->assertEquals('', $author->last_name);
        $this->assertEquals('', $author->first_name);
        $this->assertEquals('', $author->middle_name);
        $this->assertEquals('Nickname', $author->nickname);
        $this->assertEquals('Nickname', $author->name);

        $author = new Author;
        $author->name = 'Lastname  Firstname  ';

        $this->assertEquals('Lastname', $author->last_name);
        $this->assertEquals('Firstname', $author->first_name);
        $this->assertEquals('Lastname Firstname', $author->name);

        $author = new Author;
        $author->name = 'Lastname  Firstname  Middlename  ';

        $this->assertEquals('Lastname', $author->last_name);
        $this->assertEquals('Firstname', $author->first_name);
        $this->assertEquals('Middlename', $author->middle_name);
        $this->assertEquals('Lastname Firstname Middlename', $author->name);


        $author = new Author;
        $author->name = 'Last-name Firstname ';

        $this->assertEquals('Last-name', $author->last_name);
        $this->assertEquals('Firstname', $author->first_name);
        $this->assertEquals('', $author->middle_name);
        $this->assertEquals('Last-name Firstname', $author->name);
    }
}
