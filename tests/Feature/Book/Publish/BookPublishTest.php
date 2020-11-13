<?php

namespace Tests\Feature\Book\Publish;

use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Enums\StatusEnum;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Manager;
use App\Notifications\BookPublishedNotification;
use App\Sequence;
use App\User;
use App\UserOnModeration;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookPublishTest extends TestCase
{
    public function testAddForReviewHttp()
    {
        config(['activitylog.enabled' => true]);

        $this->resetCounters();

        $this->assertEquals(0, Book::getCachedOnModerationCount());
        $this->assertEquals(0, BookFile::getCachedOnModerationCount());

        $admin = User::factory()->create();
        $admin->group->check_books = true;
        $admin->push();

        $book = Book::factory()
            ->with_writer()
            ->private()
            ->with_section()
            ->lp_false()
            ->with_create_user()
            ->with_genre()
            ->create();

        $user = $book->create_user;
        $user->group->add_book = true;
        $user->group->add_book_without_check = false;
        $user->group->check_books = false;
        $user->push();

        $author = Author::factory()
            ->private()
            ->create();

        $illustrator = Author::factory()
            ->private()
            ->create();

        $translator = Author::factory()
            ->private()
            ->create();

        $book->writers()->sync([$author->id]);
        $book->translators()->sync([$translator->id]);
        $book->illustrators()->sync([$illustrator->id]);

        $sequence = Sequence::factory()->private()->create(['create_user_id' => $user->id]);

        $book->sequences()->sync([$sequence->id]);

        UpdateSequenceBooksCount::dispatch($sequence);

        $sequence->refresh();

        $this->assertEquals(1, $sequence->book_count);

        $book_file = BookFile::factory()
            ->txt()
            ->private()
            ->create(['book_id' => $book->id, 'create_user_id' => $user->id]);

        $book_keyword = BookKeyword::factory()
            ->private()
            ->create(['book_id' => $book->id, 'create_user_id' => $user->id]);

        Bus::fake();

        $response = $this->actingAs($user)
            ->get(route('books.publish', $book));
        //var_dump(session('errors'));
        $response->assertRedirect(route('books.show', $book))
            ->assertSessionHasNoErrors()
            ->assertSessionHas(['success' => __('book.added_for_check')]);

        $book->refresh();
        $sequence->refresh();
        $author->refresh();
        $illustrator->refresh();
        $translator->refresh();
        $book_file->refresh();
        $book_keyword->refresh();

        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());

        $this->assertEquals(1, Book::getCachedOnModerationCount());
        $this->assertEquals(1, BookFile::getCachedOnModerationCount());

        $this->assertTrue($book->isSentForReview());
        $this->assertTrue($sequence->isSentForReview());
        $this->assertTrue($author->isSentForReview());
        $this->assertTrue($illustrator->isSentForReview());
        $this->assertTrue($translator->isSentForReview());
        $this->assertTrue($book_file->isSentForReview());
        $this->assertTrue($book_keyword->isSentForReview());

        Bus::assertDispatched(UpdateSequenceBooksCount::class);

        $this->assertNull($book_file->formats);

        $this->assertEquals(1, $book->activities()->count());
        $activity = $book->activities()->first();
        $this->assertEquals('add_for_review', $activity->description);
        $this->assertEquals($user->id, $activity->causer_id);
        $this->assertEquals('user', $activity->causer_type);
    }

    public function resetCounters()
    {
        Book::sentOnReview()->update(['status' => StatusEnum::Accepted]);
        Book::flushCachedOnModerationCount();
        $this->assertEquals(0, Book::getCachedOnModerationCount());

        BookFile::sentOnReview()->update(['status' => StatusEnum::Accepted]);
        BookFile::flushCachedOnModerationCount();
        $this->assertEquals(0, BookFile::getCachedOnModerationCount());

        BookKeyword::sentOnReview()->update(['status' => StatusEnum::Accepted]);
        BookKeyword::flushCachedOnModerationCount();
        $this->assertEquals(0, BookKeyword::getCachedOnModerationCount());
    }

    public function testMakeAcceptedHttp()
    {
        config(['activitylog.enabled' => true]);

        $this->resetCounters();

        Notification::fake();

        $this->assertEquals(0, Book::getCachedOnModerationCount());
        $this->assertEquals(0, BookFile::getCachedOnModerationCount());

        $admin = User::factory()->create();
        $admin->group->check_books = true;
        $admin->group->add_book_without_check = false;
        $admin->group->edit_other_user_book = true;
        $admin->push();

        $user = User::factory()->create();

        $book = Book::factory()->sent_for_review()->with_section()->with_file()->lp_false()->with_genre()->create(['create_user_id' => $user->id]);

        $book->authors()->detach();
        $book->sequences()->detach();

        $author = Author::factory()->create(['create_user_id' => $user->id]);
        $author->statusSentForReview();
        $author->save();

        $illustrator = Author::factory()->create(['create_user_id' => $user->id]);
        $illustrator->statusSentForReview();
        $illustrator->save();

        $translator = Author::factory()->create(['create_user_id' => $user->id]);
        $translator->statusSentForReview();
        $translator->save();

        $book->writers()->sync([$author->id]);
        $book->translators()->sync([$translator->id]);
        $book->illustrators()->sync([$illustrator->id]);

        $sequence = Sequence::factory()->create(['create_user_id' => $user->id]);
        $sequence->statusPrivate();
        $sequence->save();

        $book->sequences()->sync([$sequence->id]);

        UpdateSequenceBooksCount::dispatch($sequence);
        $sequence->refresh();

        $this->assertEquals(1, $sequence->book_count);

        $book_file = BookFile::factory()->txt()->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
        $book_file->statusSentForReview();
        $book_file->save();

        $book_keyword = BookKeyword::factory()->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
        $book_keyword->statusSentForReview();
        $book_keyword->save();

        $this->assertTrue($admin->can('makeAccepted', $book));

        Book::flushCachedOnModerationCount();
        $this->assertEquals(1, Book::getCachedOnModerationCount());
        BookFile::flushCachedOnModerationCount();
        $this->assertEquals(1, BookFile::getCachedOnModerationCount());
        BookKeyword::flushCachedOnModerationCount();
        $this->assertEquals(1, BookKeyword::getCachedOnModerationCount());

        $response = $this->followingRedirects()
            ->actingAs($admin)
            ->get(route('books.publish', $book))
            ->assertSessionHasNoErrors()
            ->assertOk()
            ->assertSeeText(__('book.published'));

        $sequence->refresh();
        $book->refresh();

        $this->actingAs($admin)
            ->get(route('books.show', $book))
            ->assertOk()
            ->assertSeeText($book->title)
            ->assertSeeText($author->name)
            ->assertSeeText($illustrator->name)
            ->assertSeeText($translator->name)
            ->assertSeeText($sequence->name)
            ->assertSeeText($book_keyword->keyword->text);

        $this->assertEquals(StatusEnum::Accepted, $book->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $author->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $illustrator->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $translator->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $book_file->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $book_keyword->fresh()->status);

        $this->assertEquals(1, $sequence->book_count);

        $this->assertEquals([$book_file->extension], $book->formats);

        $this->assertEquals(0, Book::getCachedOnModerationCount());
        $this->assertEquals(0, BookFile::getCachedOnModerationCount());
        $this->assertEquals(0, BookKeyword::getCachedOnModerationCount());

        $this->assertEquals(1, $book->activities()->count());
        $activity = $book->activities()->first();
        $this->assertEquals('make_accepted', $activity->description);
        $this->assertEquals($admin->id, $activity->causer_id);
        $this->assertEquals('user', $activity->causer_type);

        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testPublishIfCreatorCanAddWithoutCheck()
    {
        $this->resetCounters();

        Notification::fake();

        $this->assertEquals(0, Book::getCachedOnModerationCount());
        $this->assertEquals(0, BookFile::getCachedOnModerationCount());

        $book = Book::factory()
            ->with_writer()
            ->private()
            ->with_section()
            ->with_file()
            ->lp_false()
            ->with_create_user()
            ->with_genre()
            ->create();

        $user = $book->create_user;
        $user->group->check_books = false;
        $user->group->add_book_without_check = true;
        $user->group->edit_other_user_book = false;
        $user->push();

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertSessionHasNoErrors()
            ->assertOk()
            ->assertSeeText(__('book.published'));

        $book->refresh();

        $this->assertTrue($book->isAccepted());

        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());

        Notification::assertNotSentTo(
            $book->create_user, BookPublishedNotification::class
        );
    }

    public function testMakePrivateToAccepted()
    {
        $this->resetCounters();

        $admin = User::factory()->create();
        $admin->group->check_books = true;
        $admin->group->add_book_without_check = true;
        $admin->group->edit_other_user_book = true;
        $admin->push();

        $user = User::factory()->create();

        $book = Book::factory()
            ->private()
            ->with_section()
            ->with_file()
            ->lp_false()
            ->with_genre()
            ->create(['create_user_id' => $user->id]);

        $book->authors()->detach();
        $book->sequences()->detach();

        $author = Author::factory()->create(['create_user_id' => $user->id]);
        $author->statusPrivate();
        $author->save();

        $illustrator = Author::factory()->create(['create_user_id' => $user->id]);
        $illustrator->statusPrivate();
        $illustrator->save();

        $translator = Author::factory()->create(['create_user_id' => $user->id]);
        $translator->statusPrivate();
        $translator->save();

        $book->writers()->sync([$author->id]);
        $book->translators()->sync([$translator->id]);
        $book->illustrators()->sync([$illustrator->id]);

        $sequence = Sequence::factory()->create(['create_user_id' => $user->id]);
        $sequence->statusPrivate();
        $sequence->save();

        $book->sequences()->sync([$sequence->id]);

        UpdateSequenceBooksCount::dispatch($sequence);
        $sequence->refresh();

        $this->assertEquals(1, $sequence->book_count);

        $book_file = BookFile::factory()->txt()->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
        $book_file->statusPrivate();
        $book_file->save();

        $book_keyword = BookKeyword::factory()->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
        $book_keyword->statusPrivate();
        $book_keyword->save();

        $this->assertTrue($admin->can('makeAccepted', $book));

        $response = $this->followingRedirects()
            ->actingAs($admin)
            ->get(route('books.publish', $book));

        //dump(session('errors'));

        $response->assertSessionHasNoErrors()
            ->assertOk()
            ->assertSeeText(__('book.published'));

        $sequence->refresh();
        $book->refresh();

        $this->actingAs($admin)
            ->get(route('books.show', $book))
            ->assertOk()
            ->assertSeeText($book->title)
            ->assertSeeText($author->name)
            ->assertSeeText($illustrator->name)
            ->assertSeeText($translator->name)
            ->assertSeeText($sequence->name)
            ->assertSeeText($book_keyword->keyword->text);

        $this->assertEquals(StatusEnum::Accepted, $book->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $author->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $illustrator->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $translator->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $book_file->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $book_keyword->fresh()->status);

        $this->assertEquals(1, $sequence->book_count);

        $this->assertEquals([$book_file->extension], $book->formats);

        $this->assertEquals(0, Book::getCachedOnModerationCount());
        $this->assertEquals(0, BookFile::getCachedOnModerationCount());

        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testPublishByUserOnModeate()
    {
        $this->resetCounters();

        $this->assertEquals(0, Book::getCachedOnModerationCount());
        $this->assertEquals(0, BookFile::getCachedOnModerationCount());

        $book = Book::factory()
            ->with_writer()
            ->private()
            ->with_section()
            ->with_file()
            ->lp_false()
            ->with_create_user()
            ->with_genre()
            ->create();

        $user = $book->create_user;
        $user->group->check_books = false;
        $user->group->add_book_without_check = false;
        $user->group->edit_other_user_book = false;
        $user->push();

        $user_on_moderation = UserOnModeration::factory()->create(['user_id' => $user->id]);

        $user->refresh();

        $this->assertTrue($user->isOnModeration());

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertSessionHasNoErrors()
            ->assertOk()
            ->assertSeeText(__('book.on_check'))
            ->assertSeeText(__('book.you_will_receive_a_notification_when_the_book_is_published'))
            ->assertSeeText(__('book.added_for_check'));

        $book->refresh();

        $this->assertTrue($book->isUserChangedStatus($user));
        $this->assertTrue($book->isSentForReview());

        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testBookAcceptedIfPublishedByAuthorHttp()
    {
        $author = Author::factory()->with_author_manager()->with_private_book()->create();

        $user = $author->managers->first()->user;
        $user->group->add_book = true;
        $user->push();

        $book = $author->books()->first();
        $book->create_user_id = $user->id;
        $book->is_lp = false;
        $book->save();

        $this->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('books.show', $book))
            ->assertSessionHas('success', __('book.published'));

        $book->refresh();

        $this->assertTrue($book->isAccepted());

        $this->assertTrue($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testBookSentForReviewIfPublishedByEditorHttp()
    {
        $author = Author::factory()->with_editor_manager()->with_private_book()->create();

        $user = $author->managers->first()->user;
        $user->group->add_book = true;
        $user->push();

        $book = $author->books()->first();
        $book->create_user_id = $user->id;
        $book->is_lp = false;
        $book->save();

        $this->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('books.show', $book))
            ->assertSessionHas('success', __('book.added_for_check'));

        $book->refresh();

        $this->assertTrue($book->isSentForReview());
        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testBookSentForReviewIfPublishedByNotAcceptedAuthorHttp()
    {
        $author = Author::factory()->with_author_manager()->with_private_book()->create();

        $manager = $author->managers->first();
        $manager->statusSentForReview();
        $manager->save();

        $user = $manager->user;
        $user->group->add_book = true;
        $user->push();

        $book = $author->books()->first();
        $book->create_user_id = $user->id;
        $book->is_lp = false;
        $book->save();

        $response = $this->actingAs($user)
            ->get(route('books.publish', $book));
        if (!empty(session('errors'))) {
            var_dump(session('errors'));
        }
        $response->assertSessionHasNoErrors()
            ->assertRedirect(route('books.show', $book))
            ->assertSessionHas('success', __('book.added_for_check'));

        $book->refresh();

        $this->assertTrue($book->isSentForReview());
        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testBookSentForReviewIfPublishedByAuthorButOtherAuthorExistsHttp()
    {
        $author = Author::factory()->with_author_manager()->with_private_book()->create();

        $user = $author->managers->first()->user;
        $user->group->add_book = true;
        $user->push();

        $book = $author->books()->first();
        $book->create_user_id = $user->id;
        $book->is_lp = false;
        $book->save();

        $writer = Author::factory()->create();
        $book->writers()->sync([$writer->id]);
        $book->refresh();

        $this->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('books.show', $book))
            ->assertSessionHas('success', __('book.added_for_check'));

        $book->refresh();

        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testSendToPrivateAndPublish()
    {
        $book = Book::factory()
            ->with_writer()
            ->accepted()
            ->with_section()
            ->with_file()
            ->lp_false()
            ->with_create_user()
            ->with_genre()
            ->create();

        $admin = User::factory()->create();
        $admin->group->check_books = true;
        $admin->push();

        $this->actingAs($admin)
            ->post(route('books.add_to_private', ['book' => $book]))
            ->assertRedirect();

        $this->actingAs($admin)
            ->get(route('books.publish', ['book' => $book]))
            ->assertRedirect();

        $book->refresh();

        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testAuthorManagerSentForReviewOnBookPublish()
    {
        $admin = User::factory()->admin()->create();

        $book = Book::factory()
            ->private()
            ->with_file()
            ->lp_false()
            ->with_create_user()
            ->with_genre()
            ->create();

        $user = $book->create_user;
        $user->group->add_book = true;
        $user->group->add_book_without_check = false;
        $user->group->check_books = false;
        $user->push();

        $manager = Manager::factory()->private()->character_author()->create();

        $author = $manager->manageable;
        $book->authors()->sync([$author->id]);
        $book->save();

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertOk()
            ->assertSessionHasNoErrors();

        $manager->refresh();
        $book->refresh();

        $this->assertTrue($manager->isSentForReview());
        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testDontDisableReadOrDownloadAccessForLpBooks()
    {
        $book = Book::factory()
            ->with_writer()
            ->sent_for_review()
            ->with_section()
            ->with_file()
            ->lp_true()
            ->with_genre()
            ->create();

        $admin = User::factory()->create();
        $admin->group->check_books = true;
        $admin->push();

        $this->actingAs($admin)
            ->get(route('books.publish', ['book' => $book]))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $book->refresh();

        $this->assertTrue($book->isReadAccess());
        $this->assertTrue($book->isDownloadAccess());
    }

    public function testAccessEnabledIfSectionsExistsAfterPublish()
    {
        $book = Book::factory()
            ->with_writer()
            ->private()
            ->with_section()
            ->lp_true()
            ->with_genre()
            ->with_create_user()
            ->create();

        $user = $book->create_user;
        $user->group->check_books = false;
        $user->group->add_book_without_check = true;
        $user->group->edit_other_user_book = false;
        $user->push();

        $this->assertEquals(0, $book->files()->count());
        $this->assertGreaterThan(0, $book->sections()->where('type', 'section')->count());

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertSessionHasNoErrors()
            ->assertOk()
            ->assertSeeText(__('book.published'));

        $book->refresh();

        $this->assertTrue($book->isAccepted());
        $this->assertTrue($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testAccessEnabledIfFilesExistsAfterPublish()
    {
        $book = Book::factory()
            ->with_writer()
            ->private()
            ->with_file()
            ->lp_true()
            ->with_genre()
            ->with_create_user()
            ->create();

        $user = $book->create_user;
        $user->group->check_books = false;
        $user->group->add_book_without_check = true;
        $user->group->edit_other_user_book = false;
        $user->push();

        $this->assertGreaterThan(0, $book->files()->count());
        $this->assertEquals(0, $book->sections()->where('type', 'section')->count());

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertSessionHasNoErrors()
            ->assertOk()
            ->assertSeeText(__('book.published'));

        $book->refresh();

        $this->assertTrue($book->isAccepted());
        $this->assertFalse($book->isReadAccess());
        $this->assertTrue($book->isDownloadAccess());
    }

    public function testCloseReadDownloadAcessIfNoFilesAndNoSectionsAfterPublish()
    {
        $book = Book::factory()->with_writer()->private()->lp_true()->with_create_user()->with_genre()->create();

        $user = $book->create_user;
        $user->group->check_books = false;
        $user->group->add_book_without_check = true;
        $user->group->edit_other_user_book = false;
        $user->push();

        $this->assertEquals(0, $book->files()->count());
        $this->assertEquals(0, $book->sections()->where('type', 'section')->count());

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertSessionHasNoErrors()
            ->assertOk()
            ->assertSeeText(__('book.published'));

        $book->refresh();

        $this->assertTrue($book->isAccepted());
        $this->assertFalse($book->isReadAccess());
        $this->assertFalse($book->isDownloadAccess());
    }

    public function testSeeErrorsOnPublish()
    {
        $user = User::factory()->admin()->create();

        $book = Book::factory()->private()->create();

        $book->authors()->detach();

        $response = $this->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertRedirect(route('books.edit', $book));
        //dump(session('errors'));
        $response->assertSessionHasErrors(['writers' => __('validation.required', ['attribute' => __('book.writers')])])
            ->assertSessionHas(['try_publish' => true]);

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->get(route('books.publish', $book))
            ->assertOk()
            ->assertSeeText(__('book.fix_errors_before_publish'))
            ->assertSeeText(__('validation.required', ['attribute' => __('book.writers')]));
    }

    public function testDontAddForReviewFileIfAutoCreated()
    {
        $book = Book::factory()->with_writer()->private()->with_file()->with_create_user()->with_genre()->create();

        $user = $book->create_user;
        $user->group->add_book = true;
        $user->push();

        $file = $book->files()->first();
        $file->auto_created = true;
        $file->save();

        $this->assertTrue($file->isPrivate());

        $response = $this->actingAs($user)
            ->get(route('books.publish', $book))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $file->refresh();

        $this->assertTrue($file->isAccepted());
        $this->assertTrue($file->isAutoCreated());
    }
}
