<?php

namespace Tests\Browser;

use App\Author;
use App\Book;
use App\BookFile;
use App\BookKeyword;
use App\Enums\ReadStatus;
use App\Enums\StatusEnum;
use App\Section;
use App\User;
use Illuminate\Support\Str;
use Tests\DuskTestCase;

class BookTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testUploadFile()
    {
        $this->browse(function ($user_browser) {

            $user = User::factory()->create();
            $user->group->add_book = true;
            $user->push();

            $user_browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.create'))
                ->attach('file', __DIR__.'/../Feature/Book/Books/test_95.doc.zip')
                ->press(__('common.upload'))
                ->assertSee(__('book.parse.wait'));

            $book = $user->created_books()->any()->first();

            $this->assertNotNull($book);

            $user_browser->assertUrlIs(route('books.create.description', ['book' => $book]));
        });
    }

    public function testViewPrivacy()
    {
        $this->browse(function ($user_browser, $other_user_browser) {

            $book = Book::factory()->private()->with_create_user()->with_writer()->create();

            $book = Book::any()->findOrFail($book->id);

            $other_user = User::factory()->create();

            $this->assertNotNull($book->create_user);
            $this->assertEquals($book->writers()->any()->get()->first()->create_user->id, $book->create_user->id);

            $user_browser->resize(1000, 1000)
                ->loginAs($book->create_user)
                ->visit(route('books.show', $book))
                ->assertSee($book->title)
                ->assertSee($book->writers()->any()->first()->name);

            $other_user_browser->resize(1000, 1000)
                ->loginAs($other_user)
                ->visit(route('books.show', $book))
                ->assertSee(__('book.access_denied'));
        });
    }

    public function testVote()
    {
        $this->browse(function ($user_browser) {

            $book = Book::factory()->accepted()->with_create_user()->create();

            $user = User::factory()->create();
            $user->group->vote_for_book = true;
            $user->push();

            $number = rand(2, 10);

            $user_browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.show', $book))
                ->with('#rating', function ($vote) use ($number) {
                    $vote->clickLink($number)
                        ->assertPresent('.fa-star');
                });

            $this->assertEquals($number, $book->votes()->where('create_user_id', $user->id)->first()->vote);
            /*
                        // remove vote
                        $user_browser->visit(route('books.show', $book))
                            ->clickLink(__('Delete a rating'))
                            ->with('#rating', function ($vote) {
                                $vote->assertMissing('.fa-star');
                            });

                        $this->assertNull($book->votes()->where('create_user_id', $user->id)->first());
                    */
        });
    }

    public function testAddAndRemoveFavorite()
    {
        $this->browse(function ($user_browser) {

            $book = Book::factory()->accepted()->with_create_user()->create();

            $user = User::factory()->create();

            $user_browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.show', $book))
                ->assertSee(__('common.add_to_favorites'))
                ->click('.user_library')
                ->waitForText(__('common.in_favorites'), 15)
                ->assertSee(__('common.in_favorites'));

            $this->assertEquals(1, $user->fresh()->user_lib_book_count);

            // remove
            $user_browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.show', $book))
                ->assertSee(__('common.in_favorites'))
                ->click('.user_library')
                ->waitForText(__('common.add_to_favorites'), 15)
                ->assertSee(__('common.add_to_favorites'));

            $this->assertEquals(0, $user->fresh()->user_lib_book_count);
        });
    }

    public function testReadStatus()
    {
        $this->browse(function ($user_browser) {

            $book = Book::factory()->accepted()->create();

            $user = User::factory()->create();

            $user_browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.show', $book))
                ->select('.read-status', ReadStatus::Readed)
                //->waitFor('.read-status.saving')
                //->assertSelected('.read-status', ReadStatus::Readed)
                ->waitFor('.read-status.saved')
                ->visit(route('books.show', $book));

            $user_browser->pause(5000);

            $status = $user->book_read_statuses()
                ->first();

            $this->assertEquals(ReadStatus::Readed, $status->status);
            $this->assertEquals(ReadStatus::Readed, $user_browser->value('.read-status'));
            $this->assertEquals(1, $user->fresh()->book_read_count);

            $user_browser->visit(route('books.show', $book))
                ->select('.read-status', 'null')
                //->waitFor('.read-status.saving')
                ->waitFor('.read-status.saved')
                ->visit(route('books.show', $book))
                ->assertUrlIs(route('books.show', $book));

            $user_browser->pause(5000);

            $this->assertEquals('null', $user_browser->value('.read-status'));
            $this->assertEquals(0, $user->fresh()->book_read_count);
        });
    }

    public function testAddComment()
    {
        $this->browse(function ($user_browser) {

            $book = Book::factory()->accepted()->create();

            $user = User::factory()->create();
            $user->group->add_comment = true;
            $user->push();

            $text = $this->faker->realText(300);

            $user_browser->resize(1000, 2000)
                ->loginAs($user)
                ->visit(route('books.show', $book))
                ->with('.reply-box', function ($div) use ($text) {
                    $div->waitFor('.sceditor-container', 15)
                        ->driver
                        ->executeScript('sceditor.instance(document.getElementById("bb_text")).insertText("'.$text.'");');
                    $div->press(__('common.send'));
                })
                /*
                ->waitFor('.reply-box form.loading-cap')
                ->waitUntilMissing('.reply-box form.loading-cap')
                ->waitFor('.comments.loading-cap', 20)
                */
                ->waitUntilMissing('.comments.loading-cap')
                ->with('.comments', function ($comments) use ($text) {
                    $comments->waitFor('.item', 15)
                        ->assertSee($text);
                });

            $this->assertEquals(1, $book->fresh()->comment_count);

        });
    }

    public function testSendOnReview()
    {
        $this->browse(function ($browser) {

            Book::any()
                ->where('status', StatusEnum::OnReview)
                ->update(['status' => StatusEnum::Private]);

            $book = Book::factory()->with_writer()->private()->with_create_user()->with_genre()->create();

            $book = Book::any()->findOrFail($book->id);

            $book->create_user->group->check_books = false;
            $book->create_user->push();

            $browser->resize(1000, 1000)
                ->loginAs($book->create_user)
                ->visit(route('books.show', $book))
                ->assertVisible('.dropdown-toggle')
                ->click('.dropdown-toggle')
                ->assertVisible('.dropdown-menu')
                ->with('.dropdown-menu', function ($menu) {
                    $menu->clickLink(__('book.add_for_review'));
                })
                ->assertSee(__('book.added_for_check'))
                ->assertSee($book->title);

            $book->refresh();

            $this->assertEquals(StatusEnum::OnReview, $book->status);
            $this->assertNotNull($book->status_changed_at);
            $this->assertNotNull($book->status_changed_user_id);

            $book->forceDelete();
        });
    }

    public function testSeeOnModerationCounter()
    {
        $this->browse(function ($browser) {

            $admin = User::factory()->create();
            $admin->group->check_books = true;
            $admin->push();

            $this->assertTrue($admin->can('view_on_moderation', Book::class));
            $this->assertTrue($admin->group->check_books);

            $book = Book::factory()->sent_for_review()->create();

            Book::flushCachedOnModerationCount();

            $this->assertGreaterThanOrEqual(1, Book::getCachedOnModerationCount());

            $browser->resize(1000, 1000)
                ->loginAs($admin)
                ->visit(route('profile', $admin))
                ->with('#sidebar', function ($sidebar) {
                    $sidebar->waitFor('[href="#admin_functions"]', 15)
                        ->assertPresent('[href="#admin_functions"]')
                        ->assertPresent('#admin_functions .list-group-item')
                        ->waitForText(__('navbar.admin_functions'), 10)
                        ->assertVisible('[href="#admin_functions"]')
                        ->click('[href="#admin_functions"]')
                        ->whenAvailable('#admin_functions.show', function ($menu) {
                            $menu->whenAvailable('a[href="'.route('books.on_moderation').'"]', function ($button) {
                                $button->with('.badge', function ($badge) {

                                    $count = Book::getCachedOnModerationCount();

                                    $badge->assertSee($count);
                                });
                            });
                        });
                });

            $book->delete();
        });
    }

    public function testAcceptBookIfOnReview()
    {
        $this->browse(function ($admin_browser) {

            $book = Book::factory()->with_writer()->sent_for_review()->with_create_user()->with_genre()->create(['title' => Str::random(8)]);

            $book_keyword = BookKeyword::factory()
                ->sent_for_review()
                ->create([
                    'book_id' => $book->id
                ]);

            $admin = User::factory()->administrator()->create();

            $this->assertTrue($book->fresh()->isSentForReview());

            $admin_browser->resize(1000, 1000)
                ->loginAs($admin)
                ->visit(route('books.show', $book))
                ->assertSee(__('book.on_check'))
                ->click('.dropdown-toggle')
                ->whenAvailable('.dropdown-menu', function ($menu) {
                    $menu->clickLink(__('book.publish'));
                })
                ->assertSee(__('book.published'))
                ->assertSee($book->title);

            $this->assertTrue($book->fresh()->isAccepted());
        });
    }

    public function testRejectBookIfOnReview()
    {
        $this->browse(function ($admin_browser) {

            $book = Book::factory()->with_writer()->sent_for_review()->with_create_user()->with_genre()->create();

            $admin = User::factory()->create();
            $admin->group->check_books = true;
            $admin->push();

            $this->assertTrue($book->fresh()->isSentForReview());

            $admin_browser->resize(1000, 1000)
                ->loginAs($admin)
                ->visit(route('books.show', $book))
                ->assertSee(__('book.on_check'))
                ->click('.dropdown-toggle')
                ->whenAvailable('.dropdown-menu', function ($menu) {
                    $menu->clickLink(__('book.add_to_private'));
                })
                ->assertSee(__('book.book_will_be_sent_to_the_personal_library_that_published_it'))
                ->press(__('book.remove_the_publication'))
                ->assertSee(__('book.rejected_and_sended_to_private'))
                ->assertSee(__('book.access_denied'));

            $this->assertTrue($book->fresh()->isPrivate());
        });
    }

    public function testReadDownloadAccessDisable()
    {
        $this->browse(function ($browser) {

            $book = Book::factory()->create();
            $book->statusAccepted();
            $book->save();

            $admin = User::factory()->create();
            $admin->group->book_secret_hide_set = true;
            $admin->push();

            $this->assertTrue($admin->can('change_access', $book));

            $this->assertTrue($book->fresh()->isReadAccess());
            $this->assertTrue($book->fresh()->isDownloadAccess());

            $browser->resize(1000, 1000)
                ->loginAs($admin)
                ->visit(route('books.show', $book))
                ->click('.dropdown-toggle')
                ->whenAvailable('.dropdown-menu', function ($menu) {
                    $menu->clickLink(__('book.close_read_download_access'));
                })
                ->assertSee(__('book.access_closed'))
                ->assertSee(__('book.read_access_disabled'))
                ->assertSee(__('book.download_access_disabled'));

            $this->assertFalse($book->fresh()->isReadAccess());
            $this->assertFalse($book->fresh()->isDownloadAccess());
        });
    }

    public function testReadDownloadAccessDisableThroughForm()
    {
        $this->browse(function ($browser) {

            $book = Book::factory()->create();
            $book->statusAccepted();
            $book->save();

            $admin = User::factory()->create();
            $admin->group->book_secret_hide_set = true;
            $admin->push();

            $this->assertTrue($admin->can('change_access', $book));

            $this->assertTrue($book->fresh()->isReadAccess());
            $this->assertTrue($book->fresh()->isDownloadAccess());

            $browser->resize(1000, 1000)
                ->loginAs($admin)
                ->visit(route('books.show', $book))
                ->click('.dropdown-toggle')
                ->whenAvailable('.dropdown-menu', function ($menu) {
                    $menu->clickLink(__('book.read_download_access'));
                });

            $browser->assertChecked('read_access')
                ->assertChecked('download_access')
                ->uncheck('read_access')
                ->uncheck('download_access')
                ->type('secret_hide_reason', $this->faker->realText(100))
                ->press(__('common.save'))
                ->assertSee(__('book.access_settings_have_been_successfully_changed'))
                ->assertNotChecked('read_access')
                ->assertNotChecked('download_access');

            $this->assertFalse($book->fresh()->isReadAccess());
            $this->assertFalse($book->fresh()->isDownloadAccess());
        });
    }

    public function testShowMoreAnnotation()
    {
        $this->browse(function ($browser) {

            $book = Book::factory()->with_annotation()->with_genre()->create(['title' => uniqid()]);

            $book->annotation->content = $this->faker->realText(500);
            $book->push();
            $book->refresh();

            $browser->resize(1000, 1000)
                ->visit(route('books', ['search' => $book->title]))
                ->with('[data-id="'.$book->id.'"]', function ($container) use ($book) {
                    $container->assertSee($book->title)
                        ->assertSee(mb_substr(strip_tags($book->annotation->getContent()), 0, 100))
                        ->assertSee(__('common.show_more'))
                        ->clickLink(__('common.show_more'))
                        ->waitFor('.collapse.show')
                        ->assertSee(strip_tags($book->annotation->getContent()));
                });
        });
    }

    public function testKeywordsTooltip()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();
            $user->group->book_keyword_vote = true;
            $user->push();

            $book = Book::factory()->with_keyword()->create();

            $book_keyword = $book->book_keywords->first();

            $browser->loginAs($user)
                ->resize(1000, 1000)
                ->visit(route('books.show', $book->id))
                ->with('.keywords', function ($container) use ($book_keyword) {

                    $keyword_title = $book_keyword->keyword->text;

                    $container->assertSee($keyword_title)
                        ->click('.keyword');
                })
                ->whenAvailable('.popover.show', function ($popover) use ($book_keyword) {
                    $popover->assertSee(__('book.all_books'))
                        ->assertVisible('.up')
                        ->assertVisible('.down')
                        ->click('.up')
                        ->waitFor('.up.btn-success')
                        ->assertVisible('.down.btn-light')
                        ->pause(2000);

                    $book_keyword->refresh();

                    $this->assertEquals(1, $book_keyword->rating);

                    $popover->click('.down')
                        ->waitFor('.down.btn-danger')
                        ->assertVisible('.up.btn-light')
                        ->pause(2000);

                    $book_keyword->refresh();

                    $this->assertEquals(-1, $book_keyword->rating);
                })
                ->assertPresent('header')
                ->click('header')
                ->waitUntilMissing('.popover.show')
                ->assertMissing('.popover.show');
        });
    }

    public function testPassAgeRestrictionAnswerNo()
    {
        $this->browse(function ($browser) {

            $age = 18;

            $book = Book::factory()->create(['age' => 18]);

            $browser->visit(route('books.show', $book))
                ->waitFor('#askAgeModal', 10)
                ->assertVisible('#askAgeModal')
                ->with('#askAgeModal', function ($modal) use ($age) {
                    $modal->assertSee(__('common.are_you_older_than', ['age' => $age]))
                        ->press(__('common.no'))
                        ->assertRouteIs('home');
                });
        });
    }

    public function testPassAgeRestrictionAnswerYes()
    {
        $this->browse(function ($browser) {

            $age = 18;

            $book = Book::factory()->create(['age' => 18]);

            $browser->visit(route('books.show', $book))
                ->waitFor('#askAgeModal', 10)
                ->assertVisible('#askAgeModal');

            $this->assertStringContainsString('blur', $browser->attribute('main', 'class'));

            $browser->whenAvailable('#askAgeModal', function ($modal) use ($age) {
                $modal->assertSee(__('common.are_you_older_than', ['age' => $age]))
                    ->press(__('common.yes'));
            })
                ->waitUntilMissing('#askAgeModal');

            $this->assertStringNotContainsString('blur', $browser->attribute('main', 'class'));

            $browser->pause(2000);

            $browser->assertHasCookie('can_pass_age')
                ->visit(route('books.show', $book))
                ->assertMissing('#askAgeModal');

            $this->assertStringNotContainsString('blur', $browser->attribute('main', 'class'));
        });
    }

    public function testPassAgeRestrictionWithBornDate()
    {
        $this->browse(function ($browser) {

            $age = 18;

            $book = Book::factory()->create();
            $book->statusAccepted();
            $book->save();

            $user = User::factory()->create(['born_date' => null])
                ->fresh();

            $section = Section::factory()->create(['book_id' => $book->id])
                ->fresh();

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sections.show', ['book' => $book->id, 'section' => $section->inner_id]))
                ->assertSee(strip_tags($section->pages()->first()->content))
                ->visit(route('books.show', ['book' => $book->id]))
                ->assertMissing('#askAgeModal');

            $book->age = $age;
            $book->save();

            // not logined

            $browser->resize(1000, 1000)
                ->visit(route('books.sections.show', ['book' => $book->id, 'section' => $section->inner_id]))
                ->whenAvailable('#askAgeModal', function ($modal) use ($age) {
                    $modal->assertSee(__('common.are_you_older_than', ['age' => $age]));
                })
                ->visit(route('books.show', ['book' => $book->id]))
                ->whenAvailable('#askAgeModal', function ($modal) use ($age) {
                    $modal->assertSee(__('common.are_you_older_than', ['age' => $age]));
                });

            // if birth day not set

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sections.show', ['book' => $book->id, 'section' => $section->inner_id]))
                ->whenAvailable('#askAgeModal', function ($modal) use ($age) {
                    $modal->assertSee(__('common.are_you_older_than', ['age' => $age]));
                })
                ->visit(route('books.show', ['book' => $book->id]))
                ->whenAvailable('#askAgeModal', function ($modal) use ($age) {
                    $modal->assertSee(__('common.are_you_older_than', ['age' => $age]));
                });

            $user->born_date = now()->subYear($age)->addDay();
            $user->save();

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sections.show', ['book' => $book->id, 'section' => $section->inner_id]))
                ->whenAvailable('#askAgeModal', function ($modal) use ($age) {
                    $modal->assertSee(__('common.are_you_older_than', ['age' => $age]));
                })
                ->visit(route('books.show', ['book' => $book->id]))
                ->whenAvailable('#askAgeModal', function ($modal) use ($age) {
                    $modal->assertSee(__('common.are_you_older_than', ['age' => $age]));
                });


            $user->born_date = now()->subYear($age)->subDay();
            $user->save();

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sections.show', ['book' => $book->id, 'section' => $section->inner_id]))
                ->assertMissing('#askAgeModal')
                ->visit(route('books.show', ['book' => $book->id]))
                ->assertDontSee(__('common.are_you_older_than', ['age' => $age]));
        });
    }

    public function testToSellBookYouNeedRequest()
    {
        $this->browse(function ($browser) {

            $author = Author::factory()->with_author_manager()->with_book_for_sale()->create();

            $manager = $author->managers->first();
            $book = $author->books->first();
            $user = $manager->user;
            $book->create_user()->associate($user);
            $book->save();

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sales.edit', ['book' => $book]))
                ->assertSee(__('book.to_sell_books_you_need_to_request'))
                ->assertSee(__('book.sent_request'))
                ->clickLink(__('book.sent_request'))
                ->assertUrlIs(route('authors.sales.request', ['author' => $manager->manageable]));
        });
    }

    public function testRemoveFromSale()
    {
        $this->browse(function ($browser) {

            $author = Author::factory()->with_author_manager_can_sell()->with_book_for_sale_purchased()->create();

            $manager = $author->managers->first();
            $book = $author->books->first();
            $user = $manager->user;
            $book->create_user()->associate($user);
            $book->save();

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sales.edit', ['book' => $book]))
                ->assertSee(__('book.remove_from_sale'))
                ->click('.remove_from_sale')
                ->whenAvailable('.bootbox-confirm .modal-content', function ($dialog) {
                    $dialog->assertSee(__('book.remove_from_sale_warning', ['days' => config('litlife.book_removed_from_sale_cooldown_in_days')]))
                        ->assertSee(__('common.cancel'))
                        ->click('[data-bb-handler="cancel"]');
                })
                ->waitUntilMissing('.bootbox-confirm .modal-content');

            $browser->click('.remove_from_sale')
                ->whenAvailable('.bootbox-confirm .modal-content', function ($dialog) {
                    $dialog->assertSee(__('common.i_confirm'))
                        ->click('[data-bb-handler="confirm"]');
                })
                ->assertUrlIs(route('books.sales.edit', ['book' => $book]))
                ->assertSee(__('book.removed_from_sale'));

            $this->assertTrue($book->fresh()->isRejected());
        });
    }

    public function testSeeFileIfBookPrivateAndUserCreatorOfBookFile()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $file = BookFile::factory()
                ->txt()
                ->private()
                ->create(['create_user_id' => $user->id]);

            $book = Book::factory()
                ->private()
                ->create(['create_user_id' => $user->id]);

            $file->book()->associate($book);
            $file->save();

            $this->assertTrue($file->isPrivate());
            $this->assertTrue($book->isPrivate());

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.show', ['book' => $book]))
                ->assertVisible('.files .file')
                ->with('.files .file', function ($file_item) use ($file) {
                    $file_item->with('.btn', function ($btn) use ($file) {
                        $btn->assertSee($file->extension);
                    });
                });
        });
    }

    public function testSeeFileIfBookSentForReviewAndBookFileOnReviewAndAuthUserCreatorOfBookFile()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $file = BookFile::factory()->txt()->sent_for_review()->create(['create_user_id' => $user->id]);

            $book = Book::factory()->sent_for_review()->create();

            $file->book()->associate($book);
            $file->save();

            $this->assertTrue($file->isSentForReview());
            $this->assertTrue($book->isSentForReview());

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.show', ['book' => $book]))
                ->assertVisible('.files .file')
                ->with('.files .file', function ($file_item) use ($file) {
                    $file_item->with('.btn', function ($btn) use ($file) {
                        $btn->assertSee($file->extension);
                    });
                });
        });
    }

    public function testSeeFileIfBookAcceptedAndBookFileAcceptedAndAuthUserCreatorOfBookFile()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $file = BookFile::factory()->txt()->accepted()->create(['create_user_id' => $user->id]);

            $book = Book::factory()->accepted()->create();

            $file->book()->associate($book);
            $file->save();

            $this->assertTrue($file->isAccepted());
            $this->assertTrue($book->isAccepted());

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.show', ['book' => $book]))
                ->assertVisible('.files .file')
                ->with('.files .file', function ($file_item) use ($file) {
                    $file_item->with('.btn', function ($btn) use ($file) {
                        $btn->assertSee($file->extension);
                    });
                });
        });
    }

    public function testDontSeeFileIfBookAcceptedAndBookFileOnReview()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $file = BookFile::factory()->txt()->sent_for_review()->create();

            $book = Book::factory()->accepted()->create();

            $file->book()->associate($book);
            $file->save();

            $this->assertTrue($file->isSentForReview());
            $this->assertTrue($book->isAccepted());

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.show', ['book' => $book]))
                ->assertMissing('.files .file');
        });
    }

    public function testSeeFileIfBookSentForReviewAndBookFileAccepted()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $file = BookFile::factory()->txt()->accepted()->create();

            $book = Book::factory()->sent_for_review()->create();

            $file->book()->associate($book);
            $file->save();

            $this->assertTrue($file->isAccepted());
            $this->assertTrue($book->isSentForReview());

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.show', ['book' => $book]))
                ->assertVisible('.files .file')
                ->with('.files .file', function ($file_item) use ($file) {
                    $file_item->with('.btn', function ($btn) use ($file) {
                        $btn->assertSee($file->extension);
                    });
                });
        });
    }

    public function testScrollToFiles()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $file = BookFile::factory()->txt()->accepted()->create();

            $book = $file->book;

            $browser->resize(1000, 400)
                ->loginAs($user)
                ->visit(route('books.show', ['book' => $book]).'#files')
                ->assertVisible('#files');
        });
    }

    public function testSeeBookIfCanSeeDeleted()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();
            $user->group->see_deleted = true;
            $user->push();

            $book = Book::factory()->create();
            $book->delete();

            $browser->resize(1000, 400)
                ->loginAs($user)
                ->visit(route('books.show', ['book' => $book]))
                ->with('.title', function ($title) use ($book) {
                    $title->assertSee(__('book.deleted'))
                        ->assertSee($book->title);
                });
        });
    }

    public function testDontSeeBookIfCantSeeDeleted()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();
            $user->group->see_deleted = false;
            $user->push();

            $book = Book::factory()->create();
            $book->delete();

            $browser->resize(1000, 400)
                ->loginAs($user)
                ->visit(route('books.show', ['book' => $book]))
                ->with('.title', function ($title) use ($book) {
                    $title->assertSee(__('book.deleted'))
                        ->assertDontSee($book->title);
                });
        });
    }

    public function testSeeBookFileWasCreatedBySite()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->admin()->create();

            $file = BookFile::factory()->txt()->create();
            $file->auto_created = true;
            $file->save();

            $book = $file->book;

            $browser->resize(1000, 400)
                ->loginAs($user)
                ->visit(route('books.show', ['book' => $book]))
                ->with('.files', function ($files) {
                    $files->assertSee(__('book_file.was_created_by_the_site'));
                });

            $file->auto_created = false;
            $file->save();

            $browser->visit(route('books.show', ['book' => $book]))
                ->with('.files', function ($files) {
                    $files->assertDontSee(__('book_file.was_created_by_the_site'));
                });
        });
    }
}
