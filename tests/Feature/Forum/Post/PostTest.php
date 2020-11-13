<?php

namespace Tests\Feature\Forum\Post;

use App\Post;
use Illuminate\Database\QueryException;
use Tests\TestCase;

class PostTest extends TestCase
{
    public function testFulltextSearch()
    {
        $author = Post::FulltextSearch('Время&—&детство!')->get();

        $this->assertTrue(true);
    }

    public function testBBEmpty()
    {
        $post = Post::factory()->create();

        $this->expectException(QueryException::class);

        $post->bb_text = '';
        $post->save();
    }

    /*
        public function testIsSamePostExists()
        {
            $user = User::factory()->create();

            $topic = Topic::factory()->create();

            $text = $this->faker->realText(100);

            $this->actingAs($user)
                ->post(route('posts.store', ['topic' => $topic->id]), [
                    'bb_text' => $text
                ])
                ->assertSessionHasNoErrors()
                ->assertRedirect();

            $this->actingAs($user)
                ->post(route('posts.store', ['topic' => $topic->id]), [
                    'bb_text' => $text
                ])
                ->assertSessionHasNoErrors()
                ->assertRedirect();

            $this->actingAs($user)
                ->post(route('posts.store', ['topic' => $topic->id]), [
                    'bb_text' => $text
                ])
                ->assertSessionHasErrors(['bb_text' => __('post.you_leave_same_posts')]);
        }
        */


}
