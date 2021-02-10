<?php

namespace Tests\Feature\Author\Manager;

use App\Manager;
use App\User;
use Illuminate\Support\Str;
use Tests\TestCase;

class AuthorManagerOnReviewTest extends TestCase
{
    public function testSeeAuthorIsNotPublished()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->sent_for_review()->create();

        $author = $manager->manageable;
        $author->statusPrivate();
        $author->save();

        $this->actingAs($admin)
            ->get(route('managers.on_check'))
            ->assertOk()
            ->assertDontSeeText(__('manager.the_author_is_not_published'));
    }

    public function testIfAuthorDeleted()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()
            ->character_author()
            ->sent_for_review()
            ->create();

        $author = $manager->manageable;
        $author->statusAccepted();
        $author->save();

        $author->delete();

        $this->actingAs($admin)
            ->get(route('managers.on_check'))
            ->assertOk();

        $author->forceDelete();

        $this->actingAs($admin)
            ->get(route('managers.on_check'))
            ->assertOk();
    }

    public function testDontShowPrivate()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()
            ->character_author()
            ->private()
            ->create();

        $author = $manager->manageable;
        $author->first_name = Str::random(10);
        $author->save();

        $this->actingAs($admin)
            ->get(route('managers.on_check'))
            ->assertOk()
            ->assertDontSeeText($author->first_name);
    }
}
