<?php

namespace Tests\Feature\Author\Manager;

use App\Author;
use App\Manager;
use App\User;
use Tests\TestCase;

class AuthorVerificationRequest extends TestCase
{
    public function testIsOk()
    {
        $author = Author::factory()
            ->create();

        $user = User::factory()->create();
        $user->group->author_editor_request = true;
        $user->push();

        $this->actingAs($user)
            ->get(route('authors.verification.request', $author))
            ->assertOk();

        $comment = $this->faker->realText(100);

        $this->actingAs($user)
            ->post(route('authors.verification.request_save', $author),
                ['comment' => $comment])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('authors.show', $author))
            ->assertSessionHas(['success' => __('manager.request_has_been_sent')]);

        $manager = $author->managers()->first();

        $this->assertNotNull($manager);
        $this->assertEquals($user->id, $manager->create_user_id);
        $this->assertEquals($user->id, $manager->user_id);
        $this->assertEquals('author', $manager->character);
        $this->assertEquals($author->id, $manager->manageable_id);
        $this->assertEquals('author', $manager->manageable_type);
        $this->assertEquals($comment, $manager->comment);
        $this->assertTrue($manager->isSentForReview());
    }

    public function testIfAuthorPrivate()
    {
        $author = Author::factory()
            ->private()
            ->create();

        $user = $author->create_user;
        $user->group->author_editor_request = true;
        $user->push();

        $comment = $this->faker->realText(300);

        $this->actingAs($user)
            ->post(route('authors.verification.request_save', $author), [
                'comment' => $comment
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect()
            ->assertSessionHas(['success' => __('manager.request_is_saved_and_will_be_sent_for_review_after_the_authors_publication')]);

        $manager = $author->managers()->first();

        $this->assertNotNull($manager);
        $this->assertTrue($manager->isPrivate());
    }

    public function testCanIfRequestRejected()
    {
        $manager = Manager::factory()
            ->character_author()
            ->rejected()
            ->create();

        $user = $manager->user;
        $user->group->author_editor_request = true;
        $user->push();

        $author = $manager->manageable;

        $comment = $this->faker->realText(100);

        $this->actingAs($user)
            ->post(route('authors.verification.request_save', $author),
                ['comment' => $comment])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('authors.show', $author))
            ->assertSessionHas(['success' => __('manager.request_has_been_sent')]);

        $manager = $author->managers()
            ->where('id', '!=', $manager->id)
            ->first();

        $this->assertNotNull($manager);
        $this->assertEquals($user->id, $manager->create_user_id);
        $this->assertEquals($user->id, $manager->user_id);
        $this->assertEquals('author', $manager->character);
        $this->assertEquals($author->id, $manager->manageable_id);
        $this->assertEquals('author', $manager->manageable_type);
        $this->assertEquals($comment, $manager->comment);
        $this->assertTrue($manager->isSentForReview());
    }
}
