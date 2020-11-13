<?php

namespace Tests\Feature\Artisan;

use App\Post;
use App\Topic;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RefreshLatestTopicsAtBottomTest extends TestCase
{
    public function testRefresh()
    {
        $post = Post::factory()->create();

        $topic = $post->topic;

        Artisan::call('refresh:latest_topics_at_bottom');

        $latestTopics = Topic::cachedLatestTopics();
        $this->assertEquals($topic->id, $latestTopics->first()->id);

        DB::table('topics')
            ->where('id', $topic->id)
            ->delete();

        $latestTopics = Topic::cachedLatestTopics();
        $this->assertEquals($topic->id, $latestTopics->first()->id);

        Artisan::call('refresh:latest_topics_at_bottom');

        $latestTopics = Topic::cachedLatestTopics();

        if (!empty($latestTopics->first())) {
            $this->assertNotEquals($topic->id, $latestTopics->first()->id);
        } else {
            $this->assertNull($latestTopics->first());
        }
    }
}
