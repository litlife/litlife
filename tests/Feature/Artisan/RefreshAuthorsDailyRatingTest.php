<?php

namespace Tests\Feature\Artisan;

use App\Author;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class RefreshAuthorsDailyRatingTest extends TestCase
{
    public function testCommand()
    {
        $author = Author::factory()->create();
        $author->averageRatingForPeriod->day_rating = 1;
        $author->averageRatingForPeriod->all_rating = 0;
        $author->averageRatingForPeriod->save();
        $author->rating_changed = true;
        $author->save();

        Artisan::call('refresh:authors_daily_rating', ['latest_id' => $author->id]);

        $author->refresh();

        $this->assertFalse($author->rating_changed);
    }
}
