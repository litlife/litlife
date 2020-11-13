<?php

namespace Tests\Feature\Book;

use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Enums\StatusEnum;
use App\Jobs\Sequence\UpdateSequenceBooksCount;
use App\Manager;
use App\Notifications\BookRemovedFromPublicationNotification;
use App\Sequence;
use App\User;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookAddToPrivateTest extends TestCase
{
    public function testForm()
    {
        $user = User::factory()->create();
        $user->group->check_books = true;
        $user->group->add_book_without_check = true;
        $user->push();

        $book = Book::factory()->accepted()->create();

        $response = $this->actingAs($user)
            ->get(route('books.add_to_private.form', $book))
            ->assertOk()
            ->assertSeeText(__('book.reason_for_removal_from_publication'))
            ->assertSeeText(__('book.remove_the_publication'));
    }

    public function testAddToPrivate()
    {
        config(['activitylog.enabled' => true]);

        $user = User::factory()->create();
        $user->group->check_books = true;
        $user->group->add_book_without_check = true;
        $user->push();

        $book = Book::factory()->create(['create_user_id' => $user->id]);
        $book->statusAccepted();
        $book->save();

        $book->authors()->detach();
        $book->sequences()->detach();

        $author = Author::factory()->create(['create_user_id' => $user->id]);
        $author->statusAccepted();
        $author->save();

        $illustrator = Author::factory()->create(['create_user_id' => $user->id]);
        $illustrator->statusAccepted();
        $illustrator->save();

        $translator = Author::factory()->create(['create_user_id' => $user->id]);
        $translator->statusAccepted();
        $translator->save();

        $translator2 = Author::factory()->create(['create_user_id' => $user->id]);
        $translator2->statusSentForReview();
        $translator2->save();

        $book->writers()->sync([$author->id]);
        $book->translators()->sync([$translator->id, $translator2->id]);
        $book->illustrators()->sync([$illustrator->id]);

        $sequence = Sequence::factory()->create(['create_user_id' => $user->id]);
        $sequence->statusPrivate();
        $sequence->save();
        $book->sequences()->sync([$sequence->id]);
        UpdateSequenceBooksCount::dispatch($sequence);

        $this->assertEquals(1, $sequence->fresh()->book_count);

        $book_file = BookFile::factory()->txt()->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
        $book_file->statusAccepted();
        $book_file->save();

        $book_keyword = BookKeyword::factory()->create(['book_id' => $book->id, 'create_user_id' => $user->id]);
        $book_keyword->statusAccepted();
        $book_keyword->save();

        $this->assertTrue($user->can('addToPrivate', $book));

        $reason = $this->faker->realText(100);

        $response = $this->followingRedirects()
            ->actingAs($user)
            ->post(route('books.add_to_private', $book), ['reason_for_removal_from_publication' => $reason]);

        //dump(session('errors'));
        $response->assertOk()
            ->assertSessionHasNoErrors()
            ->assertSeeText(__('book.rejected_and_sended_to_private'));

        $sequence->refresh();
        $book->refresh();

        $this->assertEquals(StatusEnum::Private, $book->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $author->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $illustrator->fresh()->status);
        $this->assertEquals(StatusEnum::Accepted, $translator->fresh()->status);
        $this->assertEquals(StatusEnum::Private, $translator2->fresh()->status);
        $this->assertEquals(StatusEnum::Private, $book_file->fresh()->status);
        $this->assertEquals(StatusEnum::Private, $book_keyword->fresh()->status);

        $this->assertEquals(1, $sequence->book_count);

        $this->assertEquals([$book_file->extension], $book->formats);

        $this->actingAs($user)
            ->get(route('books.show', $book))
            ->assertOk()
            ->assertSeeText($author->name)
            ->assertSeeText($illustrator->name)
            ->assertSeeText($translator->name)
            ->assertSeeText($sequence->name)
            ->assertSeeText($book_keyword->name);

        $this->assertEquals(1, $book->activities()->count());
        $activity = $book->activities()->first();
        $this->assertEquals('add_to_private', $activity->description);
        $this->assertEquals($user->id, $activity->causer_id);
        $this->assertEquals('user', $activity->causer_type);
        $this->assertEquals($reason, $activity->getExtraProperty('reason'));
    }

    public function testSeeBookAddToPrivateActivityLog()
    {
        config(['activitylog.enabled' => true]);

        $admin = User::factory()->admin()->create();

        $book = Book::factory()->create();

        $reason = $this->faker->realText(100);

        activity()
            ->performedOn($book)
            ->withProperties([
                'reason' => $reason
            ])
            ->log('add_to_private');

        $activity = $book->activities()->first();

        $this->actingAs($admin)
            ->get(route('books.activity_logs', ['book' => $book]))
            ->assertOk()
            ->assertSeeText(__('activity_log.description_subject_type.book.add_to_private'))
            ->assertSeeText($reason);
    }

    public function testNotification()
    {
        config(['activitylog.enabled' => true]);

        Notification::fake();

        $admin = User::factory()->admin()->create();

        $book = Book::factory()->with_create_user()->create();

        $reason = $this->faker->realText(100);

        $this->assertTrue($admin->can('addToPrivate', $book));

        $response = $this->actingAs($admin)
            ->post(route('books.add_to_private', $book), ['reason_for_removal_from_publication' => $reason])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('books.show', $book));

        $book->refresh();

        Notification::assertSentTo(
            $book->create_user,
            BookRemovedFromPublicationNotification::class,
            function ($notification, $channels) use ($book, $reason) {

                $this->assertContains('database', $channels);

                $array = $notification->toArray($book, $reason);

                $this->assertEquals(__('notification.book_removed_from_publication.subject'), $array['title']);

                $this->assertEquals(__('notification.book_removed_from_publication.line', [
                    'book_title' => $book->title,
                    'writers_names' => optional($book->writers()->first())->name,
                    'reason' => $reason
                ]), $array['description']);

                $this->assertEquals(route('books.show', $book), $array['url']);

                return $notification->book->id == $book->id;
            }
        );
    }

    public function testCantRemoveFromPrivateIfBookDeleted()
    {
        $admin = User::factory()->admin()->create();

        $book = Book::factory()->create();
        $book->delete();

        $this->assertFalse($admin->can('addToPrivate', $book));
    }

    public function testRemoveTheNotVerifiedInTheVerificationCheck()
    {
        $admin = User::factory()->admin()->create();

        $book = Book::factory()->sent_for_review()->with_create_user()->create();

        $manager = Manager::factory()->sent_for_review()->character_author()->create();

        $author = $manager->manageable;
        $author->statusSentForReview();
        $author->save();

        $book->authors()->sync([$author->id]);
        $book->save();

        $this->assertTrue($author->isSentForReview());
        $this->assertTrue($book->isSentForReview());
        $this->assertTrue($manager->isSentForReview());

        $response = $this->actingAs($admin)
            ->post(route('books.add_to_private', $book))
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $manager->refresh();

        $this->assertTrue($manager->isPrivate());
    }

    public function testReturnToPrivateIfBookPurchased()
    {
        $user = User::factory()->create();
        $user->group->check_books = true;
        $user->push();

        $book = Book::factory()->create();
        $book->bought_times_count = 0;
        $book->push();

        $this->assertTrue($user->can('addToPrivate', $book));

        $book = Book::factory()->create();
        $book->bought_times_count = 1;
        $book->push();

        $this->assertFalse($user->can('addToPrivate', $book));
    }

}
