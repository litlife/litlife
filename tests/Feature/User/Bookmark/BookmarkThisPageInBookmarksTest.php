<?php

namespace Tests\Feature\User\Bookmark;

use App\Bookmark;
use Tests\TestCase;

class BookmarkThisPageInBookmarksTest extends TestCase
{
    public function testCurrentPageInBookmarkExists()
    {
        $url = '/test/test?test=test';

        $bookmark = Bookmark::factory()->create(['url' => $url]);

        $this->get($url)
            ->assertNotFound();

        $this->assertEquals($url, $bookmark->url);

        $this->assertNotNull($bookmark->create_user->thisPageInBookmarks);
    }

    public function testCurrentPageInBookmarkNotExists()
    {
        $url = '/test/test';

        $bookmark = Bookmark::factory()->create(['url' => $url]);

        $this->get($url.'/test')
            ->assertNotFound();

        $this->assertNull($bookmark->create_user->thisPageInBookmarks);
    }
}
