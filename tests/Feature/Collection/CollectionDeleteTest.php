<?php

namespace Tests\Feature\Collection;

use App\Collection;
use Tests\TestCase;

class CollectionDeleteTest extends TestCase
{
    public function testDeleteConfirmationPage()
    {
        $collection = Collection::factory()->create();

        $user = $collection->create_user;

        $this->actingAs($collection->create_user)
            ->get(route('collections.delete.confirmation', $collection))
            ->assertOk()
            ->assertViewIs('collection.delete_confirmation')
            ->assertViewHas('collection', $collection)
            ->assertSeeText(__('Delete'));
    }

    public function testDelete()
    {
        $collection = Collection::factory()->create();

        $user = $collection->create_user;

        $this->actingAs($collection->create_user)
            ->delete(route('collections.destroy', $collection))
            ->assertRedirect(route('collections.index'))
            ->assertSessionHas('success', __('The collection was successfully deleted'));

        $this->assertSoftDeleted($collection);
    }

    public function testDeleteAjax()
    {
        $collection = Collection::factory()->create();

        $user = $collection->create_user;

        $this->actingAs($collection->create_user)
            ->ajax()
            ->delete(route('collections.destroy', $collection))
            ->assertOk();

        $collection->refresh();
        $user->refresh();

        $this->assertSoftDeleted($collection);
        $this->assertEquals(0, $user->data->created_collections_count);
    }

    public function testRestoreAjax()
    {
        $collection = Collection::factory()->create();

        $user = $collection->create_user;

        $collection->delete();

        $this->actingAs($collection->create_user)
            ->ajax()
            ->delete(route('collections.destroy', $collection))
            ->assertOk();

        $collection->refresh();
        $user->refresh();

        $this->assertFalse($collection->trashed());
        $this->assertEquals(1, $user->data->created_collections_count);
    }

    public function testCommentsDeletedAndRestoredWithCollection()
    {
        $collection = Collection::factory()->with_comment()->create();

        $comment = $collection->comments()->first();

        $collection->delete();

        $comment->refresh();

        $this->assertTrue($comment->trashed());

        $collection->restore();

        $comment->refresh();

        $this->assertFalse($comment->trashed());
    }
}
