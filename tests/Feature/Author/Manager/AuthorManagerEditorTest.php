<?php

namespace Tests\Feature\Author\Manager;

use App\Author;
use App\User;
use Tests\TestCase;

class AuthorManagerEditorTest extends TestCase
{
	public function testAuthorizationExceptionWithMessage()
	{
		$user = factory(User::class)
			->create();

		$author = factory(Author::class)
			->create();

		$response = $this->actingAs($user)
			->get(route('authors.editor.request', $author))
			->assertForbidden();

		$exception = $response->original->getData()['exception'];

		$this->assertEquals(__('Submission of new applications for editors authors is closed'), $exception->getMessage());
	}
}
