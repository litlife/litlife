<?php

namespace Tests\Browser;

use App\Book;
use App\BookKeyword;
use App\Keyword;
use App\User;
use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class KeywordsTest extends DuskTestCase
{
	/** @test */

	public function testUserCanAddOrRemoveBookKeyword()
	{
		$this->browse(function (Browser $browser) {

			$user = User::factory()->create();
			$user->group->book_keyword_add = true;
			$user->group->save();

			$title = Str::random(6);

			$book = Book::factory()->accepted()->create();

			$browser->resize(1200, 2080);

			$keyword = Keyword::factory()->create();

			// create book keyword
			$browser->loginAs($user)
				->visit(route('books.show', $book))
				->assertSee($title)
				->clickLink(__('book_keyword.add'))
				->waitFor('.keywords')
				->select2('select.keywords', $keyword->text, 15)
				//->waitFor('.select2-dropdown .select2-results__options')
				//->click('select2-results__option select2-results__option--highlighted')
				->press(__('common.attach'))
				->assertSee($keyword->text);
		});
	}

	/** @test */

	public function testAdminCanModerateKeyword()
	{
		$this->browse(function (Browser $browser) {

			$browser->resize(1200, 2080);

			$admin = User::factory()->create();
			$admin->group->book_keyword_moderate = true;
			$admin->group->save();

			$book_keyword = BookKeyword::factory()->on_review()->create();

			// book keyword approve
			$browser->loginAs($admin)
				->visit(route('book_keywords.on_moderation'))
				->with('.keyword[data-id="' . $book_keyword->id . '"]', function ($keyword) {
					$keyword
						->assertSee(__('common.approve'))
						->click('.approve');
				})
				->visit(route('books.show', $book_keyword->book))
				->assertSee($book_keyword->keyword->text);

			$book_keyword->refresh();
			$this->assertTrue($book_keyword->isAccepted());
		});

	}
}
