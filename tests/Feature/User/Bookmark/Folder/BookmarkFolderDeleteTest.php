<?php

namespace Tests\Feature\User\Bookmark\Folder;

use App\BookmarkFolder;
use Tests\TestCase;

class BookmarkFolderDeleteTest extends TestCase
{
    public function testDeleteHttp()
    {
        $folder = BookmarkFolder::factory()->create();

        $response = $this->actingAs($folder->create_user)
            ->delete(route('bookmark_folders.destroy', ['bookmark_folder' => $folder->id]))
            ->assertStatus(200);

        $folder->refresh();

        $response->assertJson($folder->toArray());

        $this->assertSoftDeleted($folder);
    }

    public function testRestoreHttp()
    {
        $folder = BookmarkFolder::factory()->create();

        $folder->delete();

        $response = $this->actingAs($folder->create_user)
            ->delete(route('bookmark_folders.destroy', ['bookmark_folder' => $folder->id]))
            ->assertStatus(200);

        $folder->refresh();

        $response->assertJson($folder->toArray());

        $this->assertFalse($folder->trashed());
    }
}
