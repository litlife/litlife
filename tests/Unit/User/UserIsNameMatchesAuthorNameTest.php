<?php

namespace Tests\Unit\User;

use App\Author;
use App\User;
use PHPUnit\Framework\TestCase;

class UserIsNameMatchesAuthorNameTest extends TestCase
{
	public function testTrueIfMatch()
	{
		$user = new User();
		$user->last_name = '   Васил\'ий';
		$user->first_name = 'Иванов';
		$user->nick = '';

		$author = new Author();
		$author->last_name = 'васил\'ий';
		$author->first_name = 'иванов  ';
		$author->nickname = '';

		$this->assertTrue($user->isNameMatchesAuthorName($author));
	}

	public function testFalseIfDidntMatch()
	{
		$user = new User();
		$user->last_name = 'Василий';
		$user->first_name = 'Иванов';
		$user->nick = '';

		$author = new Author();
		$author->last_name = 'василий';
		$author->first_name = 'иван';
		$author->nickname = '';

		$this->assertFalse($user->isNameMatchesAuthorName($author));
	}

	public function testTrueIfNickMatch()
	{
		$user = new User();
		$user->last_name = '';
		$user->first_name = '';
		$user->nick = ' NIck';

		$author = new Author();
		$author->last_name = '';
		$author->first_name = '';
		$author->nickname = 'niCK ';

		$this->assertTrue($user->isNameMatchesAuthorName($author));
	}

	public function testFalseIfLastAndFirstNameEmpty()
	{
		$user = new User();
		$user->last_name = '';
		$user->first_name = '';

		$author = new Author();
		$author->last_name = '';
		$author->first_name = '';

		$this->assertFalse($user->isNameMatchesAuthorName($author));
	}

	public function testFalseIfNickNameEmpty()
	{
		$user = new User();
		$user->last_name = '';
		$user->first_name = '';
		$user->nick = '';

		$author = new Author();
		$author->last_name = '';
		$author->first_name = '';
		$author->nickname = ' ';

		$this->assertFalse($user->isNameMatchesAuthorName($author));
	}
}
