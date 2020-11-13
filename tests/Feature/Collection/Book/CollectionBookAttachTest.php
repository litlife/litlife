<?php

namespace Tests\Feature\Collection\Book;

use App\Book;
use App\Collection;
use App\User;
use Tests\TestCase;

class CollectionBookAttachTest extends TestCase
{
    public function testAttachBook()
    {
        $collection = Collection::factory()->accepted()->create(['who_can_add' => 'everyone']);

        $this->assertNull($collection->latest_updates_at);

        $book = Book::factory()->create();

        $user = User::factory()->admin()->create();

        $number = rand(1, 200);
        $comment = $this->faker->realText(200);

        $this->actingAs($user)
            ->post(route('collections.books.attach', ['collection' => $collection]), [
                'book_id' => $book->id,
                'number' => $number,
                'comment' => $comment
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('collections.books', $collection))
            ->with('success', __('The book was successfully added to the collection'));

        $collection->refresh();

        $book2 = $collection->books()->first();

        $this->assertEquals(1, $collection->books_count);
        $this->assertEquals($book->id, $book2->id);
        $this->assertEquals($number, $book2->collected_book->number);
        $this->assertEquals($comment, $book2->collected_book->comment);
        $this->assertEquals($user->id, $book2->collected_book->create_user_id);
        $this->assertNotNull($collection->latest_updates_at);
    }
}
