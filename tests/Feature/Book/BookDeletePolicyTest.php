<?php

namespace Tests\Feature\Book;

use App\Attachment;
use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Notifications\BookDeletedNotification;
use App\Section;
use App\User;
use App\UserPurchase;
use Carbon\Carbon;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class BookDeletePolicyTest extends TestCase
{
    public function testCantDeleteIfBookPurchasedAndRemovedFromSaleAndTimeoutNotPassed()
    {
        $author = Author::factory()
            ->with_author_manager_can_sell()
            ->with_book_removed_from_sale()
            ->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->bought_times_count = 1;
        $book->save();
        $book->refresh();

        $this->assertTrue($book->isRejected());

        $this->assertFalse($user->can('delete', $book));
    }

    public function testCanDeleteIfBookPurchasedAndRemovedFromSaleAndTimeoutPassed()
    {
        $days = config('litlife.book_removed_from_sale_cooldown_in_days');

        $author = Author::factory()
            ->with_author_manager_can_sell()
            ->with_book_removed_from_sale()
            ->create();

        $user = $author->managers->first()->user;
        $book = $author->books->first();
        $book->bought_times_count = 1;
        $book->save();
        $book->refresh();

        $this->assertTrue($book->isRejected());

        $this->travelTo(now()->addDays($days)->addHour());

        $this->assertTrue($user->can('delete', $book));
    }
}
