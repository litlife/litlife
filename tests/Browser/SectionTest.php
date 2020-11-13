<?php

namespace Tests\Browser;

use App\Author;
use App\Book;
use App\Enums\StatusEnum;
use App\Section;
use App\User;
use Illuminate\Support\Str;
use Tests\DuskTestCase;

class SectionTest extends DuskTestCase
{
    /**
     * A Dusk test example.
     *
     * @return void
     */
    public function testCreate()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();

            $book = Book::factory()->create([
                'create_user_id' => $user->id,
                'status' => StatusEnum::Private
            ]);

            $title = Str::random(10);
            $text = $this->faker->realText(300);

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sections.create', $book))
                ->value('[name=title]', $title);
            $browser->driver->executeScript("CKEDITOR.instances['content'].setData('".$text."');");
            $browser->pause(500)
                ->press(__('common.save'))
                ->waitForText(__('section.add_new_chapter'))
                ->assertSee(__('section.add_new_chapter'));

            $book->refresh();

            $this->assertEquals($book->sections_count, 1);

            $book->forceDelete();
        });
    }

    public function testEdit()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->admin()->create();

            $book = Book::factory()->with_section()->create();

            $section = $book->sections()->first();

            $new_title = $this->faker->realText(100);
            $new_text = '<p>'.$this->faker->realText(300).'</p>';

            $browser->loginAs($user)
                ->visit(route('books.sections.index', $book))
                ->with('.section[data-id="'.$section->id.'"][data-inner-id="'.$section->inner_id.'"]', function ($item) {
                    $item->click('.btn-group')
                        ->with('.dropdown-menu', function ($dropdown_menu) {
                            $dropdown_menu->assertSee(mb_strtolower(__('common.edit')))
                                ->clickLink(__('common.edit'));
                        });
                })
                ->value('[name=title]', $new_title)
                ->value('[name=content]', $new_text)
                ->press(__('common.save'))
                ->waitForText(__('common.data_saved'), 15)
                ->assertSee(__('common.data_saved'));

            $section->refresh();

            $this->assertEquals($new_title, $section->title);
            $this->assertEquals($new_text, $section->getContent());
        });
    }

    public function testUploadImage()
    {
        $this->browse(function ($browser) {

            $section = Section::factory()->create();

            $browser->resize(1200, 2000)
                ->loginAs($section->book->create_user)
                ->visit(route('books.sections.edit', ['book' => $section->book, 'section' => $section->inner_id]))
                ->whenAvailable('#cke_content', function ($item) {
                    $item->click('.cke_button__image');
                })
                ->whenAvailable('.cke_dialog', function ($dialog) {
                    $dialog->click('[cke_last]')
                        ->waitFor('iframe')
                        ->assertVisible('iframe');
                })
                ->switchFrame('iframe.cke_dialog_ui_input_file')
                ->assertVisible('form')
                ->attach('upload', __DIR__.'/images/test.jpeg')
                ->switchFrame()
                ->whenAvailable('.cke_dialog', function ($dialog) {
                    $dialog->assertVisible('.cke_dialog_ui_fileButton')
                        ->click('.cke_dialog_ui_fileButton');
                });

            $browser->whenAvailable('.cke_dialog_image_url', function ($dialog_image_url) use ($section) {
                $dialog_image_url->value('input', $section->book->attachments->first()->url);
            })
                ->click('.cke_dialog_ui_button_ok')
                ->pause(1000)
                ->press(__('common.save'))
                ->assertSee(__('common.data_saved'));

            $browser->visit(route('books.sections.show', ['book' => $section->book, 'section' => $section->inner_id]))
                ->assertVisible('img[src="'.$section->book->attachments->first()->url.'"]');

            $section->book->forceDelete();
        });
    }

    public function testView()
    {
        $this->browse(function ($browser) {

            $section = Section::factory()->create();

            $browser->resize(1000, 1000)
                ->loginAs($section->book->create_user)
                ->visit(route('books.sections.show', ['book' => $section->book, 'section' => $section->inner_id]))
                ->assertSee(strip_tags($section->pages->first()->content));

            $section->book->forceDelete();
        });
    }

    public function testDeleteAndRestore()
    {
        $this->browse(function ($browser) {

            $section = Section::factory()->create();

            $browser->resize(1000, 1000)
                ->loginAs($section->book->create_user)
                ->visit(route('books.sections.index', $section->book))
                ->with('.section[data-id="'.$section->id.'"][data-inner-id="'.$section->inner_id.'"]', function ($item) {
                    $item->click('.btn-group')
                        ->with('.dropdown-menu', function ($dropdown_menu) {
                            $dropdown_menu->assertSee(mb_strtolower(__('common.delete')))
                                ->clickLink(__('common.delete'));
                        })
                        ->waitFor('.transparency')
                        ->assertVisible('.transparency');
                });

            $section = $section->fresh();

            $this->assertTrue($section->trashed());

            $browser->with('.section[data-id="'.$section->id.'"][data-inner-id="'.$section->inner_id.'"]', function ($item) {
                $item->click('.btn-group')
                    ->with('.dropdown-menu', function ($dropdown_menu) {
                        $dropdown_menu->assertSee(mb_strtolower(__('common.restore')))
                            ->clickLink(__('common.restore'));
                    })
                    ->waitUntilMissing('.transparency');
            });

            $section = $section->fresh();

            $this->assertFalse($section->trashed());
        });
    }

    public function testMoveToNote()
    {
        $this->browse(function ($browser) {

            $book = Book::factory()->private()->with_create_user()->create();

            $user = $book->create_user;

            $section = Section::factory()->create(['book_id' => $book->id]);

            $section2 = Section::factory()->create(['book_id' => $book->id]);

            $book->refresh();

            $this->assertEquals(2, $book->sections_count);
            $this->assertEquals(0, $book->notes_count);

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sections.index', $book))
                ->assertDontSee(__('section.move_to_notes'))
                ->with('.list-group-item[data-id="'.$section->id.'"]', function ($item) {
                    $item->click('.dropdown-toggle')
                        ->whenAvailable('.dropdown-menu', function ($menu) {
                            $menu->click('.move-to-notes');
                        });
                })
                ->waitUntilMissing('.list-group-item[data-id="'.$section->id.'"]')
                ->visit(route('books.notes.index', $book));

            $book->refresh();

            $this->assertEquals(1, $book->sections_count);
            $this->assertEquals(1, $book->notes_count);

        });
    }

    public function testChangePosition()
    {
        $this->browse(function ($browser) {

            $book = Book::factory()->with_create_user()->private()->create();

            $user = $book->create_user;

            $section = Section::factory()->create(['book_id' => $book->id]);

            $section2 = Section::factory()->create(['book_id' => $book->id]);

            $book->refresh();

            $sections = $book->sections()->defaultOrder()->get();

            $this->assertEquals($section->id, $sections[0]->id);
            $this->assertEquals($section2->id, $sections[1]->id);

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sections.index', $book))
                ->with('.list-group-item[data-id="'.$section2->id.'"]', function ($item) {
                    $item->click('.dropdown-toggle')
                        ->dragUp('.handle', 500);
                })
                ->press(__('section.save_position'))
                ->visit(route('books.sections.index', $book));

            $book->refresh();

            $sections = $book->sections()->defaultOrder()->get();

            $this->assertEquals($section->id, $sections[1]->id);
            $this->assertEquals($section2->id, $sections[0]->id);
            /*
                        $this->assertEquals(0, $book->sections_count);
                        $this->assertEquals(2, $book->notes_count);

            */
        });
    }

    public function testATagSpaces()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->create();
            $user->group->not_show_ad = true;
            $user->push();

            $uniqid = uniqid();

            $section = Section::factory()->create(['content' => '«<a href="http://example.com">'.$uniqid.'</a>» текст текст текст']);
            $section->refresh();
            $book = $section->book;
            $book->statusAccepted();
            $book->save();

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sections.show', ['book' => $section->book, 'section' => $section->inner_id]))
                ->assertSee('«'.$uniqid.'»');
        });
    }

    public function testSeeNextPreviousPageSection()
    {
        $this->browse(function ($browser) {

            $user = User::factory()->admin()->create();

            $book = Book::factory()->create();

            $section1 = Section::factory()
                ->with_three_pages()
                ->create(['book_id' => $book->id]);

            $section2 = Section::factory()
                ->with_three_pages()
                ->create(['book_id' => $book->id]);

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sections.show', [
                    'book' => $book->id,
                    'section' => $section1->inner_id,
                    'page' => 1
                ]));

            $browser->with('#prev_next_navigation', function ($navigation) {

                $navigation->assertMissing('[rel="prev"]')
                    ->assertVisible('[rel="next"]')
                    ->with('[rel="next"]', function ($nextButton) {
                        $nextButton->assertSee(__('section.next_page'));
                    });

                $navigation->click('[rel="next"]');
            });

            $this->assertUrl($browser, route('books.sections.show', ['book' => $book, 'section' => $section1->inner_id, 'page' => 2]));

            $browser->with('#prev_next_navigation', function ($navigation) {

                $navigation->assertVisible('[rel="prev"]')
                    ->with('[rel="prev"]', function ($nextButton) {
                        $nextButton->assertSee(__('section.previous_page'));
                    })
                    ->assertVisible('[rel="next"]')
                    ->with('[rel="next"]', function ($nextButton) {
                        $nextButton->assertSee(__('section.next_page'));
                    });

                $navigation->click('[rel="next"]');
            });

            $this->assertUrl($browser, route('books.sections.show', ['book' => $book, 'section' => $section1->inner_id, 'page' => 3]));

            $browser->with('#prev_next_navigation', function ($navigation) use ($section2) {

                $navigation->assertVisible('[rel="prev"]')
                    ->with('[rel="prev"]', function ($nextButton) {
                        $nextButton->assertSee(__('section.previous_page'));
                    })
                    ->assertVisible('[rel="next"]')
                    ->with('[rel="next"]', function ($nextButton) use ($section2) {
                        $nextButton->assertSee(__('section.next_section').' "'.$section2->title.'"');
                    });

                $navigation->click('[rel="next"]');
            });

            $this->assertUrl($browser, route('books.sections.show', ['book' => $book, 'section' => $section2->inner_id, 'page' => 1]));

            $browser->with('#prev_next_navigation', function ($navigation) use ($section1) {

                $navigation->assertVisible('[rel="prev"]')
                    ->with('[rel="prev"]', function ($nextButton) use ($section1) {
                        $nextButton->assertSee(__('section.previous_section').' "'.$section1->title.'"');
                    })
                    ->assertVisible('[rel="next"]')
                    ->with('[rel="next"]', function ($nextButton) {
                        $nextButton->assertSee(__('section.next_page'));
                    });

                $navigation->click('[rel="next"]');
            });

            $this->assertUrl($browser, route('books.sections.show', ['book' => $book, 'section' => $section2->inner_id, 'page' => 2]));

            $browser->with('#prev_next_navigation', function ($navigation) {

                $navigation->assertVisible('[rel="prev"]')
                    ->with('[rel="prev"]', function ($nextButton) {
                        $nextButton->assertSee(__('section.previous_page'));
                    })
                    ->assertVisible('[rel="next"]')
                    ->with('[rel="next"]', function ($nextButton) {
                        $nextButton->assertSee(__('section.next_page'));
                    });

                $navigation->click('[rel="next"]');
            });

            $this->assertUrl($browser, route('books.sections.show', ['book' => $book, 'section' => $section2->inner_id, 'page' => 3]));

            $browser->with('#prev_next_navigation', function ($navigation) {

                $navigation->assertVisible('[rel="prev"]')
                    ->with('[rel="prev"]', function ($nextButton) {
                        $nextButton->assertSee(__('section.previous_page'));
                    })
                    ->assertMissing('[rel="next"]');
            });
        });
    }

    public function assertUrl($browser, $url)
    {
        $this->assertEquals($browser->driver->getCurrentURL(), $url);
    }

    public function testSeeAvailableFree()
    {
        $this->browse(function ($browser) {

            $author = Author::factory()->with_author_manager_can_sell()->with_book_for_sale()->create();

            $manager = $author->managers->first();
            $book = $author->books->first();
            $user = $manager->user;

            $this->assertTrue($book->isForSale());

            $section = $book->sections()->chapter()->first();

            $section2 = Section::factory()->chapter()->create(['book_id' => $book->id]);

            $book->free_sections_count = 1;
            $book->save();
            $book->refresh();

            $this->assertEquals(2, $book->sections_count);

            $browser->resize(1000, 1000)
                ->loginAs($user)
                ->visit(route('books.sections.index', ['book' => $book]))
                ->with('.section[data-id="'.$section->id.'"][data-inner-id="'.$section->inner_id.'"]', function ($item) {
                    $item->assertSee(__('section.available_for_free'));
                })
                ->with('.section[data-id="'.$section2->id.'"][data-inner-id="'.$section2->inner_id.'"]', function ($item) {
                    $item->assertSee(__('section.available_for_a_fee'));
                });
        });
    }

    public function testDontSeeAvailableFreeOrFeeIfBookPurchased()
    {
        $this->browse(function ($browser) {

            $author = Author::factory()->with_author_manager_can_sell()->with_book_for_sale_purchased()->create();

            $manager = $author->managers->first();
            $book = $author->books->first();
            $user = $manager->user;
            $buyer = $book->boughtUsers->first();

            $this->assertTrue($book->isForSale());

            $section = $book->sections()->chapter()->first();

            $section2 = Section::factory()->chapter()->create(['book_id' => $book->id]);

            $book->free_sections_count = 1;
            $book->save();
            $book->refresh();

            $this->assertEquals(2, $book->sections_count);

            $browser->resize(1000, 1000)
                ->loginAs($buyer)
                ->visit(route('books.sections.index', ['book' => $book]))
                ->with('.section[data-id="'.$section->id.'"][data-inner-id="'.$section->inner_id.'"]', function ($item) {
                    $item->assertDontSee(__('section.available_for_free'))
                        ->assertDontSee(__('section.available_for_a_fee'));
                })
                ->with('.section[data-id="'.$section2->id.'"][data-inner-id="'.$section2->inner_id.'"]', function ($item) {
                    $item->assertDontSee(__('section.available_for_free'))
                        ->assertDontSee(__('section.available_for_a_fee'));
                });
        });
    }
}
