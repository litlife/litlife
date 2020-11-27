<?php

namespace Tests\Feature\Author\Manager;

use App\Author;
use App\AuthorSaleRequest;
use App\Manager;
use App\Notifications\AuthorManagerAcceptedNotification;
use App\Notifications\AuthorManagerRejectedNotification;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthorManagerTest extends TestCase
{
    public function testIndexHttp()
    {
        $this->get(route('authors'))
            ->assertOk();
    }

    /*
        public function testEditorRequestHttp()
        {
            $author = Author::factory()->create();

            $user = User::factory()->create();
            $user->group->author_editor_request = true;
            $user->push();

            $this->actingAs($user)
                ->get(route('authors.editor.request', $author))
                ->assertOk();

            $comment = $this->faker->realText(100);

            $this->actingAs($user)
                ->post(route('authors.editor.request_save', $author),
                    ['comment' => $comment])
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('authors.show', $author))
                ->assertSessionHas(['success' => __('manager.request_has_been_sent')]);

            $manager = $author->managers()->first();

            $this->assertNotNull($manager);
            $this->assertEquals($user->id, $manager->create_user_id);
            $this->assertEquals($user->id, $manager->user_id);
            $this->assertEquals('editor', $manager->character);
            $this->assertEquals($author->id, $manager->manageable_id);
            $this->assertEquals('author', $manager->manageable_type);
            $this->assertEquals($comment, $manager->comment);
            $this->assertTrue($manager->isSentForReview());
        }
    */

    public function testProfitPercentAttribute()
    {
        $comission = rand(10, 90);

        config(['litlife.comission' => $comission]);

        $manager = Manager::factory()->character_author()->create();

        $this->assertEquals($manager->profit_percent, 100 - $comission);

        $profit_percent = rand(10, 90);

        $manager->profit_percent = $profit_percent;
        $manager->save();

        $this->assertEquals($manager->profit_percent, $profit_percent);
    }

    public function testIsAuthorEditor()
    {
        $manager = Manager::factory()->character_author()->create();

        $this->assertTrue($manager->isAuthorCharacter());
        $this->assertFalse($manager->isEditorCharacter());

        $manager = Manager::factory()->character_editor()->create();

        $this->assertFalse($manager->isAuthorCharacter());
        $this->assertTrue($manager->isEditorCharacter());
    }

    public function testPolicyIsCantStartReviewIfAlreadyReviewStarts()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->review_starts()->create();

        $this->assertFalse($admin->can('startReview', $manager));
    }

    public function testPolicyIsCantStartReviewIfAccepted()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->accepted()->create();

        $this->assertFalse($admin->can('startReview', $manager));
    }

    public function testPolicyIsCantStartReviewIfRejected()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->rejected()->create();

        $this->assertFalse($admin->can('startReview', $manager));
    }

    public function testPolicyIsCantStopReviewIfRejected()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->rejected()->create();

        $this->assertFalse($admin->can('stopReview', $manager));
    }

    public function testPolicyIsCantStopReviewIfAccepted()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->rejected()->create();

        $this->assertFalse($admin->can('stopReview', $manager));
    }

    public function testPolicyIsCantStopReviewIfOtherUserStarts()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->review_starts()->create();

        $this->assertFalse($admin->can('stopReview', $manager));
    }

    public function testPolicyIsCantApproveIfOtherUserStarts()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->review_starts()->create();

        $this->assertFalse($admin->can('approve', $manager));
    }

    public function testPolicyIsCantDeclineIfOtherUserStarts()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->review_starts()->create();

        $this->assertFalse($admin->can('decline', $manager));
    }

    public function testPolicyCanApprove()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->review_starts()->create();
        $manager->status_changed_user_id = $admin->id;
        $manager->save();
        $manager->refresh();

        $this->assertTrue($admin->can('approve', $manager));
    }

    public function testPolicyCanDecline()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->review_starts()->create();
        $manager->status_changed_user_id = $admin->id;
        $manager->save();
        $manager->refresh();

        $this->assertTrue($admin->can('decline', $manager));
    }

    public function testPolicyCanStopReview()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->review_starts()->create();
        $manager->status_changed_user_id = $admin->id;
        $manager->save();
        $manager->refresh();

        $this->assertTrue($admin->can('stopReview', $manager));
    }

    /*
        public function testSentEditorRequestIfAuthorPrivate()
        {
            $author = Author::factory()->private()->create();

            $user = $author->create_user;
            $user->group->author_editor_request = true;
            $user->push();

            $comment = $this->faker->realText(300);

            $this->actingAs($user)
                ->post(route('authors.editor.request_save', $author), [
                    'comment' => $comment
                ])
                ->assertSessionHasNoErrors()
                ->assertRedirect()
                ->assertSessionHas(['success' => __('manager.request_is_saved_and_will_be_sent_for_review_after_the_authors_publication')]);

            $manager = $author->managers()->first();

            $this->assertNotNull($manager);
            $this->assertTrue($manager->isPrivate());
        }
    */
    public function testCantApproveIfAuthorIsNotPublished()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->review_starts()->create();
        $manager->status_changed_user_id = $admin->id;
        $manager->save();
        $manager->refresh();

        $manager->manageable->statusPrivate();
        $manager->push();

        $this->assertTrue($manager->manageable->isPrivate());

        $this->assertFalse($admin->can('approve', $manager));
        $this->assertFalse($admin->can('decline', $manager));
    }

    public function testCantStartsReviewIfAuthorIsNotPublished()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()->sent_for_review()->create();

        $manager->manageable->statusPrivate();
        $manager->push();

        $this->assertTrue($manager->manageable->isPrivate());

        $this->assertFalse($admin->can('startReview', $manager));
    }

    public function testCantSalesDisableIfNoPermissions()
    {
        $author = Author::factory()->with_author_manager_can_sell()->with_book_for_sale()->create();

        $manager = $author->managers()->first();
        $book = $author->books()->first();
        $seller = $manager->user;
        $book->create_user_id = $seller->id;
        $book->save();
        $book->refresh();

        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->can('salesDisable', $author));

        $admin->group->author_sale_request_review = false;
        $admin->push();
        $admin->refresh();

        $this->assertFalse($admin->can('salesDisable', $author));
    }

    public function testCanStartReviewIfAuthorDeleted()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()
            ->character_author()
            ->sent_for_review()
            ->create();

        $manager->manageable->delete();
        $manager->refresh();

        $this->assertTrue($admin->can('startReview', $manager));

        $manager->manageable()->forceDelete();
        $manager->refresh();

        $this->assertTrue($admin->can('startReview', $manager));
    }

    public function testCantApproveIfAuthorDeleted()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()
            ->character_author()
            ->review_starts()
            ->create();
        $manager->status_changed_user_id = $admin->id;
        $manager->save();
        $manager->refresh();

        $manager->manageable->delete();
        $manager->refresh();

        $this->assertFalse($admin->can('startReview', $manager));

        $manager->manageable()->forceDelete();
        $manager->refresh();

        $this->assertFalse($admin->can('startReview', $manager));
    }

    public function testCanDeclineIfAuthorDeleted()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()
            ->character_author()
            ->review_starts()
            ->create();
        $manager->status_changed_user_id = $admin->id;
        $manager->save();
        $manager->refresh();

        $manager->manageable->delete();
        $manager->refresh();

        $this->assertTrue($admin->can('decline', $manager));

        $manager->manageable()->forceDelete();
        $manager->refresh();

        $this->assertTrue($admin->can('decline', $manager));
    }

    public function testCanStopReviewIfAuthorDeleted()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()
            ->character_author()
            ->review_starts()
            ->create();
        $manager->status_changed_user_id = $admin->id;
        $manager->save();
        $manager->refresh();

        $manager->manageable->delete();
        $manager->refresh();

        $this->assertTrue($admin->can('stopReview', $manager));

        $manager->manageable()->forceDelete();
        $manager->refresh();

        $this->assertTrue($admin->can('stopReview', $manager));
    }

    public function testDeletePrivateManagerRequestIfAuthorDeleteOrRestore()
    {
        $admin = User::factory()->admin()->create();

        $manager = Manager::factory()
            ->character_author()
            ->private()
            ->create();

        $author = $manager->manageable;
        $author->delete();
        $manager->refresh();

        $this->assertTrue($manager->trashed());

        $author->restore();
        $manager->refresh();

        $this->assertFalse($manager->trashed());
    }

    public function testIsUserVerifiedAuthorOfBookIsTrue()
    {
        $author = Author::factory()->with_author_manager()->with_book()->create();

        $manager = $author->managers()->first();
        $user = $manager->user;
        $book = $author->books()->first();

        $this->assertTrue($book->isUserVerifiedAuthorOfBook($user));
    }

    public function testIsUserVerifiedAuthorOfBookIsFalseIfManagerIsEditor()
    {
        $author = Author::factory()->with_editor_manager()->with_book()->create();

        $manager = $author->managers()->first();
        $book = $author->books()->first();
        $user = $manager->user;

        $this->assertFalse($book->isUserVerifiedAuthorOfBook($user));
    }

    public function testIsUserVerifiedAuthorOfBookIsFalseIfManagerIsNotVerified()
    {
        $author = Author::factory()->with_author_manager()->with_book()->create();

        $manager = $author->managers()->first();
        $book = $author->books()->first();
        $user = $manager->user;
        $manager->statusSentForReview();
        $manager->save();

        $this->assertFalse($book->isUserVerifiedAuthorOfBook($user));
    }
}
