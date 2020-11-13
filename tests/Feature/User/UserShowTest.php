<?php

namespace Tests\Feature\User;

use App\Blog;
use App\Like;
use App\User;
use App\UserEmail;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserShowTest extends TestCase
{
    public function testProfile()
    {
        $user = User::factory()->create()->fresh();

        $this->get(route('profile', compact('user')))
            ->assertOk();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShowHttpWrongIdParam()
    {
        $this->get(route('profile', ['user' => Str::random(8)]))
            ->assertNotFound();
    }

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testShowDeletedProfileWithBlogMessagesFixed()
    {
        $user = User::factory()->create();

        $blog = Blog::factory()->create();
        $blog->create_user_id = $user->id;
        $blog->blog_user_id = $user->id;
        $blog->save();
        $blog->refresh();
        $blog->fix();

        $user->delete();

        $this->get(route('profile', compact('user')))
            ->assertNotFound();
    }

    public function testFixedBlogPostLikeAuth()
    {
        $blog = Blog::factory()->fixed()->create();

        $like = Like::factory()->create([
            'likeable_type' => 'blog',
            'likeable_id' => $blog->id
        ]);

        $blog->refresh();

        $this->assertTrue($blog->isFixed());
        $this->assertEquals(1, $blog->like_count);

        $response = $this->get(route('profile', ['user' => $blog->create_user]))
            ->assertOk()
            ->assertViewHas('top_blog_record', $blog);

        $top_blog_record = $response->viewData('top_blog_record');
        $this->assertEquals(0, $top_blog_record->likes->count());

        $response = $this->actingAs($like->create_user)
            ->get(route('profile', ['user' => $blog->create_user]))
            ->assertOk()
            ->assertViewHas('top_blog_record', $blog);

        $top_blog_record = $response->viewData('top_blog_record');
        $this->assertEquals(1, $top_blog_record->likes->count());
    }

    public function testSeeEmailIfShowInProfile()
    {
        $email = UserEmail::factory()->show_in_profile()->create();

        $user = $email->user;

        $this->assertTrue($email->isShowInProfile());

        $this->get(route('profile', $user))
            ->assertOk()
            ->assertSeeText($email->email);
    }

    public function testDontSeeEmailIfDontShowInProfile()
    {
        $email = UserEmail::factory()->dont_show_in_profile()->create();

        $user = $email->user;

        $this->assertFalse($email->isShowInProfile());

        $this->get(route('profile', $user))
            ->assertOk()
            ->assertDontSeeText($email->email);
    }
}
