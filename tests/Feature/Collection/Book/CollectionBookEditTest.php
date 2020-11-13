<?php

namespace Tests\Feature\Collection\Book;

use App\CollectedBook;
use App\Enums\StatusEnum;
use App\User;
use Tests\TestCase;

class CollectionBookEditTest extends TestCase
{

    public function testCollectedBookUpdate()
    {
        $user = User::factory()->admin()->create();

        $collected_book = CollectedBook::factory()->create();

        $collection = $collected_book->collection;
        $collection->status = StatusEnum::Accepted;
        $collection->who_can_add = 'everyone';
        $collection->save();
        $collection->refresh();

        $this->assertNull($collection->latest_updates_at);

        $book = $collected_book->book;

        $number = rand(1, 100);
        $comment = $this->faker->realText(200);

        $this->actingAs($user)
            ->post(route('collections.books.update', ['collection' => $collection, 'book' => $book]), [
                'number' => $number,
                'comment' => $comment,
                'book_id' => $book->id
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('collections.books.edit', ['collection' => $collection, 'book' => $book]))
            ->assertSessionHas('success', __('The book data in the collection was saved successfully'));

        $collected_book->refresh();
        $collection->refresh();

        $this->assertEquals($number, $collected_book->number);
        $this->assertEquals($comment, $collected_book->comment);
        $this->assertNotNull($collection->latest_updates_at);
    }

    public function testCollectedBookEdit()
    {
        $user = User::factory()->admin()->create();

        $collected_book = CollectedBook::factory()->create();

        $collection = $collected_book->collection;
        $collection->status = StatusEnum::Accepted;
        $collection->who_can_add = 'everyone';
        $collection->save();
        $collection->refresh();

        $book = $collected_book->book;

        $this->actingAs($user)
            ->get(route('collections.books.edit', ['collection' => $collection, 'book' => $book]))
            ->assertOk();
    }
}
