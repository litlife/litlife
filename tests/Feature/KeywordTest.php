<?php

namespace Tests\Feature;

use App\Book;
use App\BookKeyword;
use App\Keyword;
use App\User;
use Tests\TestCase;

class KeywordTest extends TestCase
{
    public function testCanAddIfBookPrivateAndUserCreatorOfBook()
    {
        $user = User::factory()->create();

        $book = Book::factory()
            ->private()
            ->create([
                'create_user_id' => $user->id
            ]);

        $this->assertTrue($user->can('addKeywords', $book));
    }

    public function testCantAddIfBookPrivateAndUserNotCreatorOfBookAndAdmin()
    {
        $admin = User::factory()->create();
        $admin->group->book_keyword_add = true;
        $admin->group->save();

        $book = Book::factory()->private()->create();

        $this->assertFalse($admin->can('addKeywords', $book));
    }

    public function testCantAddIfBookAcceptedAndNoPermissions()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_add = false;
        $user->group->book_keyword_add_new_with_check = false;
        $user->group->book_keyword_moderate = false;
        $user->group->save();

        $book = Book::factory()->accepted()->create();

        $this->assertFalse($user->can('addKeywords', $book));
    }

    public function testCanAddIfBookAcceptedAndHasPermissions()
    {
        $admin = User::factory()->create();
        $admin->group->book_keyword_add = true;
        $admin->group->save();

        $book = Book::factory()->accepted()->create();

        $this->assertTrue($admin->can('addKeywords', $book));
    }

    public function testCreateHttp()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_moderate = true;
        $user->push();

        $text = uniqid();

        $this->actingAs($user)
            ->post(route('keywords.store', ['text' => $text]))
            ->assertRedirect(route('keywords.index'))
            ->assertSessionHasNoErrors();

        $keyword = $user->created_keywords()->first();

        $this->assertEquals($text, $keyword->text);
        $this->assertTrue($keyword->isAccepted());

        $this->actingAs($user)
            ->get(route('keywords.index'))
            ->assertOk();

        $text = uniqid();

        $this->actingAs($user)
            ->followingRedirects()
            ->post(route('keywords.store', ['text' => $text]))
            ->assertSeeText(__('keyword.created', ['text' => $text]));
    }

    public function testCreateIfTextExistedNotDeletedHttp()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_moderate = true;
        $user->push();

        $text = uniqid();

        $keyword = Keyword::factory()->create(['text' => $text]);

        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post(route('keywords.store', ['text' => $text]))
            ->assertSeeText(__('keyword.already_exists', ['text' => $text]));
    }

    public function testEditHttp()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_edit = true;
        $user->push();

        $text = uniqid();

        $keyword = Keyword::factory()->create(['text' => $text]);

        $response = $this->actingAs($user)
            ->get(route('keywords.edit', $keyword->id))
            ->assertOk();
    }

    public function testUpdateHttp()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_edit = true;
        $user->push();

        $text = uniqid();

        $keyword = Keyword::factory()->create();

        $response = $this->actingAs($user)
            ->patch(route('keywords.update', $keyword->id), [
                'text' => $text
            ])
            ->assertRedirect(route('keywords.index'))
            ->assertSessionHasNoErrors();

        $keyword->refresh();

        $this->assertEquals($text, $keyword->text);
    }

    public function testDeleteRestoreHttp()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_remove = true;
        $user->push();

        $keyword = Keyword::factory()->create();

        $response = $this->actingAs($user)
            ->delete(route('keywords.destroy', ['keyword' => $keyword->id]))
            ->assertRedirect(route('keywords.index'));

        $keyword->refresh();

        $this->assertSoftDeleted($keyword);

        $response = $this->actingAs($user)
            ->delete(route('keywords.destroy', ['keyword' => $keyword->id]), [],
                ['HTTP_X-Requested-With' => 'XMLHttpRequest'])
            ->assertOk();

        $keyword->refresh();

        $response->assertJson($keyword->toArray());

        $this->assertFalse($keyword->trashed());
    }

    public function testAttachExistedKeyword()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_add = true;
        $user->group->save();

        $book_keyword = BookKeyword::factory()->create();
        $book_keyword->keyword->statusAccepted();
        $book_keyword->statusAccepted();
        $book_keyword->push();

        $book = Book::factory()->create();

        $title = $book_keyword->fresh()->keyword->text;

        $this->actingAs($user)
            ->post(route('books.keywords.store', $book), [
                'keywords' => [$title]
            ])
            ->assertSessionHasNoErrors();

        $new_book_keyword = $book->book_keywords()->first();

        $this->assertEquals($new_book_keyword->keyword()->first()->id, $book_keyword->keyword()->first()->id);
        $this->assertTrue($new_book_keyword->isAccepted());
        $this->assertTrue($new_book_keyword->keyword()->first()->isAccepted());
    }

    public function testAcceptBookKeyword()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_moderate = true;
        $user->group->save();

        $book_keyword = BookKeyword::factory()->create();
        $book_keyword->keyword->statusSentForReview();
        $book_keyword->statusSentForReview();
        $book_keyword->push();

        $this->actingAs($user)
            ->get(route('books.keywords.accept', ['book' => $book_keyword->book, 'keyword' => $book_keyword]))
            ->assertSessionHasNoErrors();

        $book_keyword->refresh();

        $this->assertTrue($book_keyword->isAccepted());
        $this->assertTrue($book_keyword->keyword()->first()->isAccepted());
    }

    /*
        public function testAddNewToPrivateBook()
        {
            $user = User::factory()->create();
            $user->group->save();

            $book = Book::factory()->create(['create_user_id' => $user->id]);
            $book->statusPrivate();
            $book->save();
            $book->refresh();

            $title = uniqid();

            $this->actingAs($user)
                ->post(route('books.keywords.store', $book), [
                    'keywords' => [$title]
                ])
                ->assertSessionHasNoErrors();

            $book_keyword = $book->book_keywords()->first();

            $this->assertTrue($book_keyword->isPrivate());
            $this->assertTrue($book_keyword->keyword->isPrivate());
        }
        */

    public function testDelete()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_remove = true;
        $user->group->save();

        $book_keyword = BookKeyword::factory()->create();
        $book_keyword->keyword->statusAccepted();
        $book_keyword->statusAccepted();
        $book_keyword->push();

        $keyword = $book_keyword->keyword;

        $this->actingAs($user)
            ->delete(route('books.keywords.destroy', ['book' => $book_keyword->book, 'keyword' => $book_keyword]))
            ->assertSessionHasNoErrors();

        $this->assertNull($book_keyword->fresh());
        $this->assertNotNull($keyword->fresh());
    }

    public function testDeletePrivateKeyword()
    {
        $book_keyword = BookKeyword::factory()->create();
        $book_keyword->keyword->statusPrivate();
        $book_keyword->statusPrivate();
        $book_keyword->push();

        $keyword = $book_keyword->keyword;

        $this->actingAs($book_keyword->create_user)
            ->delete(route('books.keywords.destroy', ['book' => $book_keyword->book, 'keyword' => $book_keyword]))
            ->assertSessionHasNoErrors()
            ->assertOk();

        $this->assertNull($book_keyword->fresh());
        $this->assertNull($keyword->fresh());
    }

    public function testDeleteOnReviewKeyword()
    {
        $book_keyword = BookKeyword::factory()->create();
        $book_keyword->keyword->statusSentForReview();
        $book_keyword->statusSentForReview();
        $book_keyword->push();

        $keyword = $book_keyword->keyword;

        $this->actingAs($book_keyword->create_user)
            ->delete(route('books.keywords.destroy', ['book' => $book_keyword->book, 'keyword' => $book_keyword]))
            ->assertSessionHasNoErrors()
            ->assertOk();

        $this->assertNull($book_keyword->fresh());
        $this->assertNull($keyword->fresh());
    }

    public function testDisableAddNewKeywordIfBookNotAccepted()
    {

        $user = User::factory()->create();
        $user->group->save();

        $book = Book::factory()->create(['create_user_id' => $user->id]);
        $book->statusPrivate();
        $book->save();

        $title = uniqid();

        $this->actingAs($user)
            ->post(route('books.keywords.store', $book), [
                'keywords' => [$title]
            ])
            ->assertSessionHasNoErrors();

        $this->assertEquals(0, $book->book_keywords()->count());

        //

        $book = Book::factory()->create(['create_user_id' => $user->id]);
        $book->statusSentForReview();
        $book->save();

        $title = uniqid();

        $this->actingAs($user)
            ->post(route('books.keywords.store', $book), [
                'keywords' => [$title]
            ])
            ->assertSessionHasNoErrors();

        $this->assertEquals(0, $book->book_keywords()->count());
    }

    public function testCreatePolicy()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_add = false;
        $user->group->book_keyword_add_new_with_check = false;
        $user->group->book_keyword_moderate = false;
        $user->push();

        $this->assertFalse($user->can('create', Keyword::class));

        $user->group->book_keyword_add = true;
        $user->push();

        $this->assertFalse($user->can('create', Keyword::class));

        $user->group->book_keyword_add = true;
        $user->group->book_keyword_add_new_with_check = true;
        $user->push();

        $this->assertFalse($user->can('create', Keyword::class));

        $user->group->book_keyword_moderate = true;
        $user->push();

        $this->assertTrue($user->can('create', Keyword::class));
    }

    public function testUpdatePolicy()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_add = true;
        $user->group->book_keyword_add_new_with_check = true;
        $user->group->book_keyword_moderate = true;
        $user->push();

        $keyword = Keyword::factory()->create();

        $this->assertFalse($user->can('update', $keyword));

        $user->group->book_keyword_edit = true;
        $user->push();

        $this->assertTrue($user->can('create', $keyword));
    }

    public function testDeletePolicy()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_add = true;
        $user->group->book_keyword_add_new_with_check = true;
        $user->group->book_keyword_moderate = true;
        $user->group->book_keyword_edit = true;
        $user->push();

        $keyword = Keyword::factory()->create();

        $this->assertFalse($user->can('delete', $keyword));

        $user->group->book_keyword_remove = true;
        $user->push();

        $this->assertTrue($user->can('delete', $keyword));

        $keyword->delete();

        $this->assertFalse($user->can('delete', $keyword));
    }

    public function testRestorePolicy()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_remove = true;
        $user->push();

        $keyword = Keyword::factory()->create();

        $this->assertFalse($user->can('restore', $keyword));

        $keyword->delete();

        $this->assertTrue($user->can('restore', $keyword));
    }

    public function testViewIndexPolicy()
    {
        $user = User::factory()->create();

        $this->assertTrue($user->can('view_index', Keyword::class));
    }

    public function testViewAtSidebarPolicy()
    {
        $user = User::factory()->create();
        $user->group->book_keyword_edit = false;
        $user->push();

        $this->assertFalse($user->can('viewAtSidebar', Keyword::class));

        $user->group->book_keyword_edit = true;
        $user->push();

        $this->assertTrue($user->can('viewAtSidebar', Keyword::class));
    }
}
