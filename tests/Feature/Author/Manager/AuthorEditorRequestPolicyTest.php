<?php

namespace Tests\Feature\Author\Manager;

use App\Author;
use App\User;
use Tests\TestCase;

class AuthorEditorRequestPolicyTest extends TestCase
{
    public function testFalse()
    {
        $user = User::factory()->admin()->create();

        $author = Author::factory()->accepted()->create();

        $this->assertFalse($user->can('editorRequest', $author));
    }

    /*
        public function testFalseIfAuthorPrivate()
        {
            $user = User::factory()->admin()->create();

            $author = Author::factory()->private()->create();

            $this->assertTrue($user->can('editorRequest', $author));
        }

        public function testTrueIfAuthorHasEditorManager()
        {
            $user = User::factory()->admin()->create();

            $author = Author::factory()->with_editor_manager()->create();

            $this->assertTrue($user->can('editorRequest', $author));
        }
    */
    public function testFalseIfEditorManagerOnReview()
    {
        $author = Author::factory()->with_editor_manager()->create();

        $manager = $author->managers()->first();
        $manager->statusSentForReview();
        $manager->save();

        $user = $manager->user;
        $user->group->author_editor_request = true;
        $user->push();

        $this->assertFalse($user->can('editorRequest', $author));
    }

    /*
        public function testTrueIfOtherUserEditorManagerOnReview()
        {
            $author = Author::factory()->with_editor_manager()->create();

            $manager = $author->managers()->first();
            $manager->statusSentForReview();
            $manager->save();

            $user = User::factory()->admin()->create();

            $this->assertTrue($user->can('editorRequest', $author));
        }
    */
    public function testFalseIfUserRequestVerification()
    {
        $author = Author::factory()->with_author_manager_sent_for_review()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;
        $user->group->author_editor_request = true;
        $user->push();

        $this->assertFalse($user->can('editorRequest', $author));
    }

    public function testFalseIfAuthorManagerPrivate()
    {
        $author = Author::factory()->private()->with_author_manager()->create();

        $manager = $author->managers()->first();
        $manager->statusPrivate();
        $manager->save();

        $user = $manager->user;
        $user->group->author_editor_request = true;
        $user->push();

        $this->assertFalse($user->can('editorRequest', $author));
    }

    public function testFalseIfUserAuthor()
    {
        $author = Author::factory()->accepted()->with_author_manager()->create();

        $manager = $author->managers()->first();
        $manager->statusAccepted();
        $manager->save();

        $user = $manager->user;
        $user->group->author_editor_request = true;
        $user->push();

        $this->assertFalse($user->can('editorRequest', $author));
    }
    /*
        public function testTrueIfUserNotAuthor()
        {
            $author = Author::factory()->accepted()->with_author_manager()->create();

            $manager = $author->managers()->first();
            $manager->statusAccepted();
            $manager->save();

            $user = User::factory()->create();
            $user->group->author_editor_request = true;
            $user->push();

            $this->assertTrue($user->can('editorRequest', $author));
        }
        */
}
