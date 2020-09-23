<?php

namespace Tests\Feature;

use App\Blog;
use App\Book;
use App\BookVote;
use App\Comment;
use App\Jobs\Book\UpdateBookRating;
use App\Post;
use App\Topic;
use App\User;
use App\UsersAccessToForum;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class HomeTest extends TestCase
{
	/**
	 * A basic test example.
	 *
	 * @return void
	 */
	public function testHomeHttp()
	{
		$this->get(route('home'))
			->assertOk()
			->assertViewIs('home.popular_books')
			->assertViewHas('period', 'week')
			->assertCookieMissing('latest_route');
	}

	public function testHomeIfLatestRouteHome()
	{
		$this->withCookie('latest_route', 'home')
			->get(route('home'))
			->assertOk()
			->assertViewIs('home.popular_books')
			->assertViewHas('period', 'week')
			->assertCookieMissing('latest_route');
	}

	public function testHomeRedirectToLatestRoute()
	{
		$this->withCookie('latest_route', 'home.latest_comments')
			->get(route('home'))
			->assertRedirect(route('home.latest_comments'));
	}

	public function testLatestBooksHttp()
	{
		$this->get(route('home.latest_books'))
			->assertOk()
			->assertViewIs('home.index')
			->assertCookie('latest_route', 'home.latest_books');
	}

	public function testPopularBooksHttp()
	{
		$this->get(route('home.popular_books'))
			->assertOk()
			->assertViewIs('home.popular_books')
			->assertCookie('latest_route', 'home.popular_books');
	}

	public function testLatestPostHttp()
	{
		$this->get(route('home.latest_posts'))
			->assertOk()
			->assertViewIs('home.latest_posts')
			->assertCookie('latest_route', 'home.latest_posts');
	}

	public function testLatestCommentsHttp()
	{
		$this->get(route('home.latest_comments'))
			->assertOk()
			->assertViewIs('home.latest_comments')
			->assertCookie('latest_route', 'home.latest_comments');
	}

	public function testLatestCommentsIfCreatorDeletedHttp()
	{
		$comment = factory(Comment::class)
			->create();

		$this->get(route('home.latest_comments'))
			->assertOk()
			->assertSeeText($comment->text);

		$comment->create_user->delete();

		$this->get(route('home.latest_comments'))
			->assertOk();

		$comment->create_user->forceDelete();

		$this->get(route('home.latest_comments'))
			->assertOk();
	}

	public function testLatestCommentsIfBookPrivateHttp()
	{
		$book = factory(Book::class)
			->create();

		$comment = factory(Comment::class)
			->create(['commentable_id' => $book->id, 'commentable_type' => 'book']);

		$this->get(route('home.latest_comments'))
			->assertOk()
			->assertSeeText($comment->text);

		$book->statusSentForReview();
		$book->save();

		$this->get(route('home.latest_comments'))
			->assertOk()
			->assertSeeText($comment->text);

		$book->statusPrivate();
		$book->save();

		$this->get(route('home.latest_comments'))
			->assertOk()
			->assertDontSeeText($comment->text);
	}


	public function testLatestPostsIfPostDeletedHttp()
	{
		$post = factory(Post::class)
			->create();

		$this->get(route('home.latest_posts'))
			->assertOk()
			->assertSeeText($post->text);

		$post->delete();

		$this->get(route('home.latest_posts'))
			->assertOk();

		$post->forceDelete();

		$this->get(route('home.latest_posts'))
			->assertOk();
	}

	public function testLatestPostsIfUserDeletedHttp()
	{
		$post = factory(Post::class)
			->create();

		$this->get(route('home.latest_posts'))
			->assertOk()
			->assertSeeText($post->text);

		$post->create_user->delete();

		$this->get(route('home.latest_posts'))
			->assertOk();

		$post->create_user->forceDelete();

		$this->get(route('home.latest_posts'))
			->assertOk();
	}

	public function testLatestPostsIfTopicCreatorDeletedHttp()
	{
		$post = factory(Post::class)
			->create();

		$this->get(route('home.latest_posts'))
			->assertOk()
			->assertSeeText($post->topic->title);

		$post->topic->create_user->delete();

		$this->get(route('home.latest_posts'))
			->assertOk();

		$post->topic->create_user->forceDelete();

		$this->get(route('home.latest_posts'))
			->assertOk();
	}

	public function testLatestWallPostsHttp()
	{
		$this->get(route('home.latest_wall_posts'))
			->assertOk()
			->assertViewIs('home.latest_wall_posts')
			->assertCookie('latest_route', 'home.latest_wall_posts');
	}

	public function testLatestWallPostsIfUserDeletedHttp()
	{
		$user = factory(User::class)->create();

		$blog = factory(Blog::class)
			->create(['blog_user_id' => $user->id, 'create_user_id' => $user->id]);

		$this->get(route('home.latest_wall_posts'))
			->assertOk()
			->assertSeeText($blog->text);

		$blog->create_user->delete();

		$this->get(route('home.latest_posts'))
			->assertOk();

		$blog->create_user->forceDelete();

		$this->get(route('home.latest_posts'))
			->assertOk();

		$blog->delete();

		$this->get(route('home.latest_posts'))
			->assertOk()
			->assertDontSeeText($blog->text);

		$blog->forceDelete();

		$this->get(route('home.latest_posts'))
			->assertOk()
			->assertDontSeeText($blog->text);
	}

	public function testPopularBooksWithoutBlacklisterdGenreHttp()
	{
		Carbon::setTestNow(now()->addYear());

		$user = factory(User::class)
			->create();

		$book = factory(Book::class)
			->states('with_genre')
			->create();

		$book_vote = factory(BookVote::class)
			->create(['book_id' => $book->id]);

		$genre = $book->genres()->first();

		UpdateBookRating::dispatch($book);
		Artisan::call('refresh:clear_rating_for_periods');

		$this->assertNotNull($genre);

		$this->get(route('home.popular_books'))
			->assertOk()
			->assertSeeText($book->title);

		$this->actingAs($user)
			->get(route('genre_blacklist', $user))
			->assertOk();

		$this->actingAs($user)
			->followingRedirects()
			->post(route('genre_blacklist.update', $user),
				[
					'genre' => [$genre->id]
				])
			->assertSeeText(__('common.data_saved'))
			->assertOk();

		$this->get(route('home.popular_books'))
			->assertOk()
			->assertDontSeeText($book->title);
	}

	public function testLatestPostHttpSeeIfOnModeration()
	{
		$post = factory(Post::class)
			->create();

		$post_on_review = factory(Post::class)->create();
		$post_on_review->statusSentForReview();
		$post_on_review->save();

		$this->get(route('home.latest_posts'))
			->assertOk()
			->assertSeeText($post->text)
			->assertDontSeeText($post_on_review->text);
	}

	public function testViewLatestCommentsIfOnReview()
	{
		$comment = factory(Comment::class)->create();
		$comment->statusSentForReview();
		$comment->save();

		$user = factory(User::class)
			->create();

		$this->actingAs($comment->create_user)
			->get(route('home.latest_comments'))
			->assertOk()
			->assertSeeText($comment->text);

		$this->actingAs($user)
			->get(route('home.latest_comments'))
			->assertOk()
			->assertDontSeeText($comment->text);
	}

	public function testViewPrivateMessageOnLatestPosts()
	{
		$post = factory(Post::class)
			->create();

		$forum = $post->forum;
		$forum->private = true;
		$forum->save();

		$usersAccessToForum = new UsersAccessToForum;
		$usersAccessToForum->user_id = $post->create_user_id;
		$forum->user_access()->save($usersAccessToForum);

		$response = $this->actingAs($post->create_user)
			->get(route('home.latest_posts'))
			->assertSeeText($post->text);

		$other_user = factory(User::class)
			->create();

		$response = $this->actingAs($other_user)
			->get(route('home.latest_posts'))
			->assertDontSeeText($post->text);

		$response = $this
			->get(route('home.latest_posts'))
			->assertDontSeeText($post->text);
	}

	public function testViewLatestPostsIfOnReview()
	{
		$post = factory(Post::class)->create();
		$post->statusSentForReview();
		$post->save();

		$user = factory(User::class)
			->create();

		$this->actingAs($post->create_user)
			->get(route('home.latest_posts'))
			->assertSeeText($post->text);

		$this->actingAs($user)
			->get(route('home.latest_posts'))
			->assertDontSeeText($post->text);
	}

	public function testViewPrivateTopicOnUserPostsList()
	{
		$post = factory(Post::class)
			->create();

		$forum = $post->forum;
		$forum->private = true;
		$forum->save();

		$topic = $post->topic;

		$usersAccessToForum = new UsersAccessToForum;
		$usersAccessToForum->user_id = $post->create_user_id;
		$forum->user_access()->save($usersAccessToForum);

		$response = $this->actingAs($post->create_user)
			->get(route('home.latest_posts', $topic->create_user))
			->assertSeeText($topic->name);

		$other_user = factory(User::class)
			->create();

		Topic::refreshLatestTopics();

		$response = $this->actingAs($other_user)
			->get(route('home.latest_posts', $topic->create_user))
			->assertDontSeeText($topic->name);

		Topic::refreshLatestTopics();

		$response = $this
			->get(route('home.latest_posts', $topic->create_user))
			->assertDontSeeText($topic->name);
	}

}
