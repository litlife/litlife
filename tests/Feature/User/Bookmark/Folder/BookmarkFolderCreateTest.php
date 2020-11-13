<?php

namespace Tests\Feature\User\Bookmark\Folder;

use App\User;
use Tests\TestCase;

class BookmarkFolderCreateTest extends TestCase
{
    public function testStoreHttp()
    {
        $user = User::factory()->create();

        $title = $this->faker->realText(100);

        $response = $this->actingAs($user)
            ->post(route('bookmark_folders.store'), [
                'title' => $title
            ])
            ->assertRedirect();

        $folder = $user->bookmark_folders()->where('title', $title)->first();

        $this->assertEquals($title, $folder->title);
    }
}
